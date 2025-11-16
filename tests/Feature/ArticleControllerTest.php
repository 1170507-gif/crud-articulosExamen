<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_articles()
    {
        $category = Category::factory()->create();
        Article::factory()->count(5)->create(['category_id' => $category->id]);

        $response = $this->getJson(route('articles.index'));

        $response->assertStatus(200);
    }


    public function test_create_returns_view_with_categories()
    {

        Category::factory()->count(3)->create();


        $response = $this->get(route('articles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('articles.create');


        $this->assertCount(3, $response['categories']);
    }


    public function test_store_creates_article_with_valid_data()
    {

        $category = Category::factory()->create();
        $data = [
            'title' => 'Mi Primer Artículo',
            'content' => 'Este es el contenido del artículo de prueba.',
            'category_id' => $category->id,
        ];

        $response = $this->postJson(route('articles.store'), $data);

        $response->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', [
            'title' => 'Mi Primer Artículo',
            'category_id' => $category->id,
        ]);

        $this->assertCount(1, Article::all());
    }


    public function test_store_fails_with_invalid_data()
    {
        $data = [
            'title' => '',
            'content' => '',
            'category_id' => '',
        ];

        $response = $this->postJson(route('articles.store'), $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['title', 'content', 'category_id']);

        $this->assertCount(0, Article::all());
    }

   
    public function test_store_fails_when_category_does_not_exist()
    {
        $data = [
            'title' => 'Artículo Válido',
            'content' => 'Contenido válido',
            'category_id' => 999, // ID inexistente
        ];

        $response = $this->postJson(route('articles.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['category_id']);
    }

    public function test_edit_returns_correct_article_and_categories()
    {

        $category = Category::factory()->create();
        $article = Article::factory()->create(['category_id' => $category->id]);
        Category::factory()->count(2)->create();

        $response = $this->get(route('articles.edit', $article));

        $response->assertStatus(200);
        $response->assertViewIs('articles.edit');
        $response->assertViewHas('article', $article);

        $this->assertCount(3, $response['categories']);
    }


    public function test_update_modifies_article_with_valid_data()
    {

        $category = Category::factory()->create();
        $article = Article::factory()->create(['category_id' => $category->id]);


        $updatedData = [
            'title' => 'Artículo Actualizado',
            'content' => 'Contenido actualizado',
            'category_id' => $category->id,
        ];

        $response = $this->putJson(route('articles.update', $article), $updatedData);

        $response->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', $updatedData);

        $this->assertEquals('Artículo Actualizado', $article->fresh()->title);
    }

    public function test_update_fails_with_invalid_data()
    {

        $article = Article::factory()->create();

        $invalidData = [
            'title' => '',
            'content' => '',
            'category_id' => '',
        ];


        $response = $this->putJson(route('articles.update', $article), $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'content', 'category_id']);
    }


    public function test_destroy_deletes_article()
    {

        $article = Article::factory()->create();


        $response = $this->deleteJson(route('articles.destroy', $article));

        $response->assertRedirect(route('articles.index'));

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);

        $this->assertCount(0, Article::all());
    }

    public function test_store_redirects_with_success_message()
    {
        $category = Category::factory()->create();
        $data = [
            'title' => 'Nuevo Artículo',
            'content' => 'Contenido del nuevo artículo',
            'category_id' => $category->id,
        ];

        $response = $this->post(route('articles.store'), $data);

        $response->assertSessionHas('success', 'Artículo creado exitosamente.');
    }

 
    public function test_article_has_correct_category_relationship()
    {
   
        $category = Category::factory()->create(['name' => 'Categoría Test']);
        $article = Article::factory()->create(['category_id' => $category->id]);

        $this->assertEquals($category->id, $article->category->id);
        $this->assertEquals('Categoría Test', $article->category->name);
    }
}