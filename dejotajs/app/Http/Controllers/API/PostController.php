<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostShowResource;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $isPrivate = request()->input('private');
        $posts = Post::with('danceGroupMember.appUser', 'danceGroupMember.danceGroup')->orderBy('id', 'desc');

        if(isset($isPrivate) && in_array($isPrivate, [0, 1])) {
            $posts = $posts->where('private', $isPrivate);
        }
                
        $createdBefore = request()->input('created_before');
        if(isset($createdBefore)) {
            $posts = $posts->where('created_at', '<', $createdBefore);
        }

        return PostResource::collection($posts->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $validated = $request->validated();

        $post = Post::create($validated);

        return (new PostResource($post))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('danceGroupMember.appUser', 'danceGroupMember.danceGRoup');
        return new PostShowResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        $validated = $request->validated();

        $post->update($validated);

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return new PostResource($post);
    }

    // public function getPublic()
    // {
    //     $posts = Post::where('private', 0)->get();

    //     return PostResource::collection($posts);
    // }

    // public function getPrivate()
    // {
    //     $posts = Post::where('private', 1)->get();

    //     return PostResource::collection($posts);
    // }
}