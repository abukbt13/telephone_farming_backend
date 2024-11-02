<?php

namespace App\Http\Controllers\FarmManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\FarmProgressRequest;
use App\Models\Farm;
use App\Models\FarmManager;
use App\Models\FarmProgress;
use App\Models\Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FarmProgressController extends Controller
{
    public function AddFarmProgress(FarmProgressRequest $request)
    {
        $user_id = Auth::user()->id;

        $data = $request->all();
        $user_id = auth()->user()->id;
        $progress = new FarmProgress();
        $progress ->farm_id = $data['farm_id'];
        $progress->activity_name = $data['activity_name'];

        $date = Carbon::now();

        $progress->date = $date->format('m-d-y');
        $progress->description = $data['description'];
        $progress->user_id = $user_id;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');

            // Generate a unique filename with the current timestamp
            $filename = time() . '.' . $file->getClientOriginalExtension();

            // Resize and compress the image to 800x800 pixels with 70% quality
            $img = Image::make($file->getRealPath());
            $img->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio(); // Maintain aspect ratio
                $constraint->upsize(); // Prevent upsizing
            })->encode('jpg', 70); // Compress to 70% quality

            // Define the storage path inside the 'public' disk in storage
            $path = 'farm/photos/' . $filename;

            // Save the resized and compressed image to the 'storage/app/public/farm/photos' directory
            Storage::disk('public')->put($path, $img);

            // Save the filename to the database
            $progress->photos = $path; // or simply $filename if you only need the filename
        }


        $progress->save();

        return[
            'status'=>'success',
            'data' =>$progress
        ];
    }
    public function viewFarmProgress($id)
    {
        $user_id = Auth::user()->id;
        $farmprogress = FarmProgress::where('user_id',$user_id)->where('farm_id',$id)->get();

       return [
            'status' =>'success',
            'progresses' =>$farmprogress,
        ];
    }
    public function viewFarm()
    {
        $user_id = Auth::user()->id;
        $farm = FarmManager::where('manager_id', $user_id)
            ->join('farms', 'farms.id', '=', 'farm_managers.farm_id')
            ->select('farms.*')
            ->get();

        return[
            'status' =>'success',
            'data' =>$farm,
            'id' =>$user_id
        ];
    }
}
