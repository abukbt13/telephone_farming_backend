<?php

namespace App\Http\Controllers;

use App\Models\Youtube;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public function saveVideo(Request $request){
        $request->validate(['title'=>'required','link'=>'required']);
        $data = $request->all();
        $youtube = new Youtube();
        $youtube->fill($data);

        return response([
            'status'=>'success',
            'message'=>'Youtube saved successfully',
            'video'=>$youtube->save()]);
    }


    public function listVideos(){
        $videos = Youtube::all();
        return response()->json([
            'status'=>'success',
            'videos'=>$videos,
            'message' =>'Schedule listed successfully'
        ]);
    }
    public function getVideo($id)
    {
        $video = Youtube::findOrFail($id);
        return response()->json([
            'status'=>'success',
            'video'=>$video,
        ]);
    }

}
