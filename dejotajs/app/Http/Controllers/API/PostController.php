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
        // atlasām tikai publiskos postus (private = 0)
        $posts = Post::with('danceGroupMember.appUser', 'danceGroupMember.danceGroup')
            ->where('private', 0)
            ->orderBy('id', 'desc')
            ->get();

        return PostResource::collection($posts);
    }

    public function myGroupPosts(Request $request, $danceGroupId)
    {
        $user = $request->user();

        // Pārbauda, vai lietotājs pieder šai kolektīvai (jebkurā lomā)
        $isMember = $user->danceGroupMembers()
            ->where('dance_group_id', $danceGroupId)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'data' => [],
                'message' => 'Jūs nepiederat šim kolektīvam'
            ], 403);
        }

        // Iegūst visus postus šai grupai (no visiem memberiem)
        $posts = Post::with('danceGroupMember.appUser', 'danceGroupMember.danceGroup')
            ->whereHas('danceGroupMember', function ($query) use ($danceGroupId) {
                $query->where('dance_group_id', $danceGroupId);
            })
            ->orderBy('id', 'desc')
            ->get();

        // Atgriež arī grupas info
        $group = \App\Models\DanceGroup::find($danceGroupId);

        return response()->json([
            'dance_group' => $group,
            'posts' => PostResource::collection($posts)
        ]);
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


}