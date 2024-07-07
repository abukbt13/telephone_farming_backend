<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\select;

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
}
