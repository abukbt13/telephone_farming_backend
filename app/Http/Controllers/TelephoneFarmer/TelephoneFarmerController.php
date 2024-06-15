<?php

namespace App\Http\Controllers\TelephoneFarmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagerRequest;
use App\Http\Requests\FarmRequest;
use App\Models\Farm;
use App\Models\FarmManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TelephoneFarmerController extends Controller
{
    function  createFarm(FarmRequest $request){
        $farm = new Farm();
        $farm->farm_name = $request->farm_name;
        $farm->location = $request->location;
        $farm->type_of_farming = $request->type_of_farming;
        $farm->save();
        return[
            'status' =>'success',
            'data' =>$farm
        ];
    }
    function viewFarm(){
        $farm = Farm::all();
        return[
            'status' =>'success',
            'data' =>$farm
        ];
    }

    function  createManager(FarmManagerRequest $request){
        $user = new User();
        $user->email = $data['email'];
        $user->name = $data['name'];
        $user->phone = $data['phone'];
        $user->email = $data['email'];
        $user->role = "telephone_farmer";
        $user->password = Hash::make($request->password);
        $user->save();
        $farmmanager = new FarmManager()

        return[
            'status' =>'success',
            'data' =>$farm
        ];
    }
    function viewFarm(){
        $farm = Farm::all();
        return[
            'status' =>'success',
            'data' =>$farm
        ];
    }
}
