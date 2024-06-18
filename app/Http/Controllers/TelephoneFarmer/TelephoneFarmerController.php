<?php

namespace App\Http\Controllers\TelephoneFarmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagerRequest;
use App\Http\Requests\FarmRequest;
use App\Models\Farm;
use App\Models\FarmManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TelephoneFarmerController extends Controller
{
    function  createFarm(FarmRequest $request){
        $farm = new Farm();
        $farm->farm_name = $request->farm_name;
        $farm->location = $request->location;
        $farm->type_of_farming = $request->type_of_farming;
        $farm->user_id = auth()->user()->id;
        $farm->save();
        return[
            'status' =>'success',
            'data' =>$farm
        ];
    }
    function viewFarm(){
        $user_id = Auth()->user()->id;
        $farm = Farm::where('user_id',$user_id)->get();
        return[
            'status' =>'success',
            'data' =>$farm
        ];
    }

    function  createManager(FarmManagerRequest $request){
        $user_id = Auth()->user()->id;
        $data = $request->all();
//        dd($data);
        $user = new User();

        $user->name = $data['name'];
        $user->phone = $data['phone'];
        $user->email = $data['email'];
        $user->role = "farm_manager";
        $user->password = Hash::make($request->email);
        $user->save();

        $farmmanager = new FarmManager();
        $farmmanager->belong_user_id = $user_id;
        $farmmanager->user_id = $user->id;
        $farmmanager->farm_id = $data['farm_id'];
        $farmmanager->save();

        return[
            'status' =>'success',
            'manager' =>$user,
            'farmmanager' =>$farmmanager
        ];
    }

    function viewManagers(){
        $user_id = Auth::user()->id;
        $farmManagers = FarmManager::where('belong_user_id', $user_id)
            ->join('users', 'users.id', '=', 'farm_managers.user_id')
            ->join('farms', 'farms.id', '=', 'farm_managers.farm_id')
            ->select('farm_managers.*', 'farms.*','users.*') // Adjust the columns as needed
            ->get();
        return [
            'status' => 'success',
            'data' => $farmManagers
        ];
    }
}
