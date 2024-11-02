<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Youtube;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class YoutubeController extends Controller
{
    public function SaveCompressedFile(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // max 10 MB
        ]);

        // Retrieve all data from request
        $data = $request->all();

        // Create a new Photo instance
        $photo = new Photo();
        $photo->title = $data['title'];

        // Check if a photo was uploaded
        if ($request->hasFile('photo')) {
            // Get the uploaded file
            $file = $request->file('photo');
            // Define a unique name for the file and the destination folder
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images');
            // Create the destination folder if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            // Resize and compress the image (800x800 pixels and 70% quality)
            $img = Image::make($file->getRealPath());
            $img->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio(); // Maintain aspect ratio
                $constraint->upsize(); // Prevent upsizing
            })->encode('jpg', 70); // Compress to 70% quality
            // Save the compressed image in the specified folder
            $img->save($destinationPath . '/' . $filename);
            // Save the filename in the database
            $photo->photo = $filename;
        }

        // Save the photo data in the database
        $photo->save();

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Image saved successfully',
            'photo' => $photo,
        ]);
    }

    public function saveVideo(Request $request){
        $request->validate(['title'=>'required','link'=>'required','category'=>'required']);
        $data = $request->all();
        $user_id = auth()->user()->id;
        $data['user_id'] = $user_id;
        $youtube = new Youtube();
        $youtube->fill($data);
        $youtube->save();

        return response([
            'status'=>'success',
            'message'=>'Youtube saved successfully',
            'video'=>$youtube
        ]);
    }
    public function searchVideo(Request $request){
        $request->validate(['category'=>'required','search'=>'required']);
        $data = $request->all();
        if($data['category'] == 'general'){
            $video = Youtube::where('title','LIKE','%'.$data['search'].'%')->get();
        }
        else{
            $video = Youtube::where('category',$data['category'])->where('title','LIKE','%'.$data['search'].'%')->get();
        }
        return response([
            'status'=>'success',
            'message'=>'Youtube saved successfully',
            'videos'=>$video
        ]);
    }


    public function listVideos(){
        $videos = Youtube::all();
        return response()->json([
            'status'=>'success',
            'videos'=>$videos,
            'message' =>'Schedule listed successfully'
        ]);
    }
    public function myVideos(){
        $user_id = auth()->user()->id;
        $videos = Youtube::where('user_id',$user_id)->get();
        return response()->json([
            'status'=>'success',
            'videos'=>$videos,
            'message' =>'Videos links listed successfully'
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
