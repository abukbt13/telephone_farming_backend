<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
   public function storeChat(){
       $data = request()->validate([
           'message' => 'required',
       ]);
       $user_id = Auth::user()->id;
       $chat = new Chat();
       $chat->sender_id = $user_id;
       $chat->receiver_id = request('receiver_id');
       $chat->chat_id =$user_id +request('receiver_id');
       $chat ->file = request('file');
       $chat->save();
       return[
           'status' =>'success',
           'data' =>$chat
       ];
   }
}
