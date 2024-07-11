<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

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
    public function listPosts()
    {
        try {
            // Fetch all posts from the database
            $posts = Post::all();

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

        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPost(){

    }
}
