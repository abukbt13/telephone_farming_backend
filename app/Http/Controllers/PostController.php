<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
public function CreatePost(){

    // Validate the request
$request->validate([
'description' => 'required|string',
'photos' => 'array|nullable',
'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
'videos' => 'array|nullable',
'videos.*' => 'mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4|max:10000',
]);

    // Get all request data
$data = $request->all();

    // Get the authenticated user's ID
$user_id = Auth::user()->id;
$data['user_id'] = $user_id;

    // Initialize arrays to store photo and video paths
$photoPaths = [];
$videoPaths = [];

    // Check if there are any photos to upload
if ($request->hasFile('photos')) {
foreach ($request->file('photos') as $photo) {
    // Store each photo and get its path
$path = $photo->store('photos', 'public');
$photoPaths[] = $path;
}
}

// Check if there are any videos to upload
if ($request->hasFile('videos')) {
    foreach ($request->file('videos') as $video) {
        // Store each video and get its path
        $path = $video->store('videos', 'public');
        $videoPaths[] = $path;
    }
}

// Convert photo and video paths arrays to JSON
$data['photos'] = json_encode($photoPaths);
$data['videos'] = json_encode($videoPaths);

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
}
