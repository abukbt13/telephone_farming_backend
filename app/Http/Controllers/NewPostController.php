<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\PostRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewPostController extends Controller
{
    public function listPosts()
    {
        // Fetch all posts from the database
        $posts = Post::join('users', 'users.id', '=', 'posts.user_id')
            ->select('posts.*', 'users.name','users.profile')
            ->orderby('posts.created_at', 'desc')
            ->get();

        // Iterate through each post
        foreach ($posts as $post) {
            // Check if the 'photos' attribute exists and is not empty
            if (isset($post->photos) && !empty($post->photos)) {
                $post->photos = json_decode($post->photos); // Convert JSON string to PHP array
            } else {
                $post->photos = []; // Set an empty array if 'photos' is null or empty
            }
        }

        // Return JSON response with success status, message, and posts data
        return response()->json([
            'status' => 'success',
            'message' => 'List Posts',
            'posts' => $posts,
        ]);


    }
    public function CreatePost(PostRequest $request)
    {

        // Get all request data
        $data = $request->all();

        // Get the authenticated user's ID
        $user_id = Auth::user()->id;
        $data['user_id'] = $user_id;

        // Initialize an array to store photo paths
        $photoPaths = [];
        // Check if there are any photos to upload
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Generate a unique file name
                $fileName = uniqid() . '.' . $photo->getClientOriginalExtension();
                // Store the photo and get its path
                $photo->move(public_path('posts/photos'), $fileName);
                $photoPaths[] = $fileName;
            }
        }
        $data['photos'] = json_encode($photoPaths);
        $videoPaths = [];
// Check if there are any videos to upload
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                // Generate a unique file name
                $fileName = uniqid() . '.' . $video->getClientOriginalExtension();
                // Store the video and get its path
                $video->move(public_path('posts/videos'), $fileName);
                $videoPaths[] = $fileName;
            }
        }
// Convert the array of video file names to a JSON string
        $data['videos'] = json_encode($videoPaths);

        // Debug: Dump and die the processed image paths
//            dd($data['photos']);

        try {
            // Create a new Post instance and fill it with data
            $post = new Post();
            $post->fill($data);
            $post->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Post Created Successfully',
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create post',
                'error' => $e->getMessage(),
            ], 500);

        }

    }
    public function getPost($id){
        $post = Post::find($id);
        $user =User::find($post['user_id']);
        // Check if the 'photos' attribute exists and is not empty
        if (isset($post->photos) && !empty($post->photos)) {
            $post->photos = json_decode($post->photos); // Convert JSON string to PHP array
        } else {
            $post->photos = []; // Set an empty array if 'photos' is null or empty
        }
        $comment = Comment::join('users', 'users.id', '=', 'comments.user_id')
            ->select('comments.*', 'users.name','users.profile')
            ->where('post_id', $id)
            ->get();
        return response()->json([
            'status' => 'success',
            'message' => 'List Comments',
            'post' => $post,
            'user' => $user,
            'comments' => $comment,
        ]);

    }

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

        //store comment
    public function storeComment(CommentRequest $request,$post_id)
    {

        $data = $request->validated();
        $user_id = Auth::user()->id;
        $data['user_id'] = $user_id;
        $data['post_id'] = $post_id;
        $comment = new Comment();
        $comment->fill($data);
        $comment->save();

        $post = Post::find($post_id);
        $newcomment=$post->comments+1;
        $post->comments=$newcomment;
        $post->update();
        return response()->json([
            'status'=>'success',
            'message'=>'Comment added successfully',
            'comment'=>$comment
        ]);

    }

}
