<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

/**
 * @group Posts
 * @authenticated
 */
class PostController extends Controller
{

    /**
     * Index Posts
     *
     * It's paginated post list.
     *
     * @queryParam perPage integer Posts per page. Default 15. Example: 5
     * @queryParam page integer Current page. Default 1. Example: 3
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $posts = Post::paginate($request->perPage ?? 15);
        return PostResource::collection($posts);
    }

    /**
     * Get Post
     *
     * It's post detail.
     *
     * @param int $id
     * @return PostResource|\Illuminate\Http\JsonResponse
     */
    public function show(int $id): \Illuminate\Http\JsonResponse|PostResource
    {
        $post = Post::find($id);
        if (!$post) return response()->json(['code' => 404, 'message' => __('Post not found.')]);
        return new PostResource($post);
    }
}
