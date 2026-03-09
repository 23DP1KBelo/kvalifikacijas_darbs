<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostShowResource;
use App\Http\Resources\DanceGroupResource;
use App\Models\DanceGroupMember;
use Illuminate\Support\Facades\Auth;
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
            ->orderByDesc('created_at')
            ->get();

        return PostResource::collection($posts);
    }

    public function myGroupPosts(Request $request, $danceGroupId)
{
    $user = $request->user();

    $isMember = $user->danceGroupMembers()
        ->where('dance_group_id', $danceGroupId)
        ->exists();

    if (!$isMember) {
        return response()->json([
            'data' => [],
            'message' => 'Jūs nepiederat šim kolektīvam'
        ], 403);
    }

    $posts = Post::with('danceGroupMember.appUser', 'danceGroupMember.danceGroup')
        ->whereHas('danceGroupMember', function ($query) use ($danceGroupId) {
            $query->where('dance_group_id', $danceGroupId);
        })
        ->orderBy('id', 'desc')
        ->get();
        
    $group = \App\Models\DanceGroup::with('members.appUser')
        ->findOrFail($danceGroupId);

    return response()->json([
        'dance_group' => new DanceGroupResource($group),
        'posts' => PostResource::collection($posts)
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
// PostController.php
    public function store(PostRequest $request)
    {
        $validated = $request->validated();

        // Ja dance_group_member_id nav nosūtīts, atrodam to automātiski
        if (!$request->filled('dance_group_member_id')) {
            $member = DanceGroupMember::where('user_id', Auth::id())
                ->where('dance_group_id', $request->dance_group_id)
                ->firstOrFail();
            $validated['dance_group_member_id'] = $member->id;
        }

        // Ja ir attēls, vienmēr saglabā publiskajā diskā
        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('posts', 'public');
            $validated['picture'] = $path; // saglabā ceļu DB
        }

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
        $validated = $request->validated(); // Šeit validē visi nepieciešamie lauki

        $post->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'private' => $validated['private'] ?? $post->private,
            'dance_group_member_id' => $validated['dance_group_member_id'] ?? $post->dance_group_member_id,
            'picture' => $validated['picture'] ?? $post->picture
        ]);

        return new PostResource($post->load('danceGroupMember.appUser', 'danceGroupMember.danceGroup'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([ 'message' => 'Ieraksts veiksmīgi dzēsts' ], 200);
    }


}