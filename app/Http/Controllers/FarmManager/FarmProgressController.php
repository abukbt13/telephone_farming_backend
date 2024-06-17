<?php

namespace App\Http\Controllers\FarmManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\FarmProgressRequest;
use App\Models\FarmProgress;

class FarmProgressController extends Controller
{
    public function AddFarmProgress(FarmProgressRequest $request)
    {
        $data = $request->all();
        $user_id = auth()->user()->id;
        $progress = new FarmProgress();
        $progress ->farm_id = $data['farm_id'];
        $progress->type = $data['type'];
        $progress->date = $data['date'];
        $progress->description = $data['description'];
        $progress->user_id = $user_id;

        $picture = $request->file('photos');
        $pictureName = time() . '_' .  $picture->getClientOriginalName();

        $progress->photos = $pictureName;
        $picture->move(public_path('Farm/Photos'), $pictureName);

        $progress->save();

        return[
            'status'=>'success',
            'data' =>$progress
        ];
    }
    public function viewFarmProgress()
    {
        $user_id = auth()->user()->id;
        $farmprogress = FarmProgress::where('user_id',$user_id)->get();

       return [
            'status' =>'success',
            'data' =>$farmprogress,
        ];
    }
}
