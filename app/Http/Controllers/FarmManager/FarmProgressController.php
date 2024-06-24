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

        $picture = $request->file('picture');
        $pictureName = time() . '_' .  $picture->getClientOriginalName();

        $progress->photos = $pictureName;
        $picture->move(public_path('Farm/Photos'), $pictureName);

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
