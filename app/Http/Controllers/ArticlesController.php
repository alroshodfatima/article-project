<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function show(Article $article)
    {
        // Show a single article.
        return view('article.show', ['article' => $article]);
    }

    public function index()
    {
        // Render a list of articles.
        if(request('tag')){
            $articles = Tag::where('name', request('tag'))->firstOrFail()->articles;
        }else{
            $articles = Article::latest()->get();
        }

        return view('article.index', ['articles' => $articles]);
    }

    public function create()
    {
        // Shows a view to create a new resource.
        return view('article.create', ['tags' => Tag::all()]);
    }

    public function store()
    {
        // Persist the new resource.
        $this->validateArticle();

        $article = new Article(request(['title', 'excerpt', 'body']));
        $article->user_id=1;
        $article->save();

        $article->tags()->attach(request('tags'));

        return redirect(route('article.index'));
    }

    public function edit(Article $article)
    {
        // Show a view to edit an existing resource.
        return view('article.edit', compact('article'));
    }

    public function update(Article $article)
    {
        // Persist the edited resource.
        $article->update($this->validateArticle());

        return redirect(route('article.show', $article));
    }

    public function destroy()
    {
        // Delete the resource.

    }

    /**
     * @return array
     */
    public function validateArticle(): array
    {
        return request()->validate([
            'title' => 'required',
            'excerpt' => 'required',
            'body' => 'required',
            'tags' => 'exists:tags,id'
        ]);
    }
}
