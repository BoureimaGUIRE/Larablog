<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StorePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //$posts = Post::all(); Pour diminuer le nombre de requetes SQL nous allons faire la méthode du bas pour optimiser la requête
        $posts = Post::with('category', 'user')->latest()->get(); //Pour éviter d'aller chercher la catégorie de chaque post dans le foreach on la sélectionne avec le post en même temps

        return view('post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('post.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $imageName = $request->image->store('posts'); //la méthode store va créer un dossier posts dans lequel sera stockée et va générer un nom aléatoire pour cette image qui sera retourné dans la variable $imageName
        
        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imageName
        ]);

        return redirect()->route('dashboard')->with('success', 'Votre post a été crée');
    }

    /**
     * Display the specified resource.
     *c
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        
        //if (Gate::denies('update-post', $post)) ou
       //$post->user_id = 1;
       if (! Gate::allows('update-post', $post)) {
            abort(403);
        }
        $categories = Category::all();
        return view('post.edit', compact('post','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(StorePostRequest $request, Post $post)
    {
        
        $arrayUpdate = [
            'title' => $request->title,
            'content' => $request->content
        ];

        if ($request->image != null){
            $imageName = $request->image->store('posts'); 
            $arrayUpdate = array_merge($arrayUpdate, [
                'image'=>$imageName
            ]);
        }

        $post->update($arrayUpdate);

        return redirect()->route('dashboard')->with('success', 'Votre post a été modifié');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (! Gate::allows('destroy-post', $post)) {
            abort(403);
        }

        $post->delete();

         return redirect()->route('dashboard')->with('success', 'Votre post a été supprimé');

    }
}
