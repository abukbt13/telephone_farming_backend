<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
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
