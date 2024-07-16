<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function addLike(Request $request, $post_id)
    {
        $user_id = auth()->id(); // Assuming you are using Laravel's built-in authentication

        // Check if the user has already liked the post
        $existing_like = Like::where('user_id', $user_id)->where('post_id', $post_id)->first();

        if (is_null($existing_like)) {
            // Increment the like count of the post
            $post = Post::find($post_id);
            if ($post) {
                $post->likes = $post->likes + 1;
                $post->save();

                // Create a new like entry
                $like = new Like();
                $like->user_id = $user_id;
                $like->post_id = $post_id;
                $like->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Post successfully liked',
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Post not found',
                ]);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'You have already liked this post',
            ]);
        }
    }
}
