<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

   
    public function test_index_returns_all_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson(route('categories.index'));

        $response->assertStatus(200);
    }


    public function test_create_returns_view()
    {
        $response = $this->get(route('categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('categories.create');
    }

    public function test_store_creates_category_with_valid_data()
    {
        $data = [
            'name' => 'Tecnología',
            'description' => 'Artículos sobre tecnología e innovación',
        ];

        $response = $this->postJson(route('categories.store'), $data);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', $data);

        $this->assertCount(1, Category::all());
    }

    public function test_store_fails_with_invalid_data()
    {
        $data = [
            'name' => '',
            'description' => '',
        ];

        $response = $this->postJson(route('categories.store'), $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['name', 'description']);

        $this->assertCount(0, Category::all());
    }
    public function test_store_fails_when_name_exceeds_max_length()
    {
        $data = [
            'name' => str_repeat('a', 121),
            'description' => 'Descripción válida',
        ];

        $response = $this->postJson(route('categories.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }


    public function test_edit_returns_correct_category()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewIs('categories.edit');
        $response->assertViewHas('category', $category);
    }

 
    public function test_update_modifies_category_with_valid_data()
    {
        $category = Category::factory()->create();

        $updatedData = [
            'name' => 'Categoría Actualizada',
            'description' => 'Descripción actualizada',
        ];

        $response = $this->putJson(route('categories.update', $category), $updatedData);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', $updatedData);

        $this->assertEquals('Categoría Actualizada', $category->fresh()->name);
    }

  
    public function test_update_fails_with_invalid_data()
    {
        $category = Category::factory()->create();

        $invalidData = [
            'name' => '',
            'description' => '',
        ];

        $response = $this->putJson(route('categories.update', $category), $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'description']);
    }

    public function test_destroy_deletes_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        $this->assertCount(0, Category::all());
    }

    public function test_store_redirects_with_success_message()
    {
        $data = [
            'name' => 'Nueva Categoría',
            'description' => 'Descripción nueva',
        ];

        $response = $this->post(route('categories.store'), $data);

        $response->assertSessionHas('success', 'Categoría creada exitosamente.');
    }
}