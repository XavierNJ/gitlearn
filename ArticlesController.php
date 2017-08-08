<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Article;//  <---------注意这里 要加引入的模型

class ArticlesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $articles= Article::orderBy('created_at','desc')->get();

        return view('articles.index',compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:50',
        ]);

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('articles.index');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit',compact('article'));
    }

    public function update(Request $request,$id)
    {
        $this->validate($request,[
            'title' => 'required|max: 50',
        ]);

        $article = Article::findOrFail($id);

        $article->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back();
    }

    public function destroy($id)
    {
        $article= Article::findOrFail($id);
        $article-> delete();
        return back();
    }


}
