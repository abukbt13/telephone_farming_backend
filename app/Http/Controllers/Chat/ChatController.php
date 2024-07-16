<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Chat;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
   public function storeChat(Request $request,$id){
       $data = request()->validate([
           'message' => 'required',
       ]);
       $user_id = Auth::user()->id;
       $chat = new Chat();
       $chat->sender_id = $user_id;
       $chat->receiver_id = $id;
       $chat->chat_id =$user_id +$id;
       $chat->message =request('message');
       $chat ->file = request('file');
       $chat->save();
       return[
           'status' =>'success',
           'message' =>'chat stored successfully',
           'data' =>$chat
       ];
   }
   public function myChats()
   {


// Assuming you have the user ID
       $user_id = Auth::user()->id;

// Fetch the chats and include the sender and receiver details
       $chats = Chat::where('sender_id', $user_id)
           ->orWhere('receiver_id', $user_id)
           ->join('users as sender', 'chats.sender_id', '=', 'sender.id')
           ->join('users as receiver', 'chats.receiver_id', '=', 'receiver.id')
           ->select('chats.*', 'sender.name as sender_name', 'receiver.name as receiver_name')
           ->latest()
           ->get();

// Create an array to store distinct user IDs
       $distinct_user_ids = [];

// Process the chats to get distinct user IDs
       foreach ($chats as $chat) {
           if ($chat->receiver_id == $user_id) {
               if (!in_array($chat->sender_id, $distinct_user_ids)) {
                   $distinct_user_ids[] = $chat->sender_id;
               }
           } elseif ($chat->sender_id == $user_id) {
               if (!in_array($chat->receiver_id, $distinct_user_ids)) {
                   $distinct_user_ids[] = $chat->receiver_id;
               }
           }
       }

// Fetch the user details based on the distinct user IDs
       $users = User::whereIn('id', $distinct_user_ids)->select('users.name','users.phone','users.id')->get();

// Return the list of distinct users with their details
       return [
           'status' => 'success',
           'message' => 'Chats retrieved successfully',
           'users' => $users
       ];

   }
   public function getUsers(){
       // Fetch users and select specific fields
       $users = User::where('role','telephone_farmer')->orwhere('role','farm_manager')->select('id','phone','name')->get();

       // Return the list of users with their details
       return response()->json([
           'status' => 'success',
           'message' => 'Users retrieved successfully',
           'users' => $users
       ]);
   }
   public function getChat($id){
       // Fetch users and select specific fields
       $user_id = Auth::user()->id;
        $chat_id = $user_id + $id;
       $messages = Chat::where('chat_id',$chat_id)->get();
       // Return the list of users with their details
       return response()->json([
           'status' => 'success',
           'message' => 'Users retrieved successfully',
           'messages' => $messages
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

    public function getPost($id){
        $post = Post::find($id);
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
            'comments' => $comment,
        ]);

    }
}
