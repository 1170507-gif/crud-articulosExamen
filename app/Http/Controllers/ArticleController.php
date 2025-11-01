<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Http\Requests\StoreArticleRequest;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('category')->get();
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('articles.create', compact('categories'));
    }

    public function store(StoreArticleRequest $request)
    {
        Article::create($request->validated());
        return redirect()->route('articles.index')->with('success', 'Artículo creado exitosamente.');
    }

    public function edit(Article $article)
    {
        $categories = Category::all();
        return view('articles.edit', compact('article', 'categories'));
    }

    public function update(StoreArticleRequest $request, Article $article)
    {
        $article->update($request->validated());
        return redirect()->route('articles.index')->with('success', 'Artículo actualizado exitosamente.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Artículo eliminado exitosamente.');
    }
}