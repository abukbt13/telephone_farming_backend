<?php

namespace App\Http\Controllers\TelephoneFarmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagerRequest;
use App\Http\Requests\FarmRequest;
use App\Models\Farm;
use App\Models\FarmManager;
use App\Models\FarmProgress;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    function getFarm($id){
        $farm = Farm::find($id);

        $farm_managers = FarmManager::join('users','users.id','=','farm_managers.manager_id')
        ->select('farm_managers.*','users.phone','users.email','users.name')
            ->where('farm_managers.farm_id',$id)
        ->get();

        $farm_progress = FarmProgress::where('farm_id',$id)->get();

        return[
            'status' =>'success',
            'farm' =>$farm,
            'farm_managers' =>$farm_managers,
            'farm_progress' =>$farm_progress,
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
        $manager = new Manager();
        $manager->manager_id = $user['id'];
        $manager->farmer_id =$user_id;
        if ($manager->save()){
            return[
                'status' =>'success',
                'message' =>'Manager has been created',
                'manager' =>$user,
            ];
        }
        else{
            $delete = User::find($user['id'])->delete();
            return[
                'status' =>'failed',
                'message' =>'something went wrong while creating manager try again later if the problem persist contact LRC',
            ];
        }

    }

    function viewManagers(){
        $user_id = Auth::user()->id;
        $Managers = Manager::where('farmer_id', $user_id)->join('users','users.id','managers.manager_id')
            ->select('users.phone','users.email','users.name','managers.*')
            ->get();
        return [
            'status' => 'success',
            'data' => $Managers
        ];
    }
    function assignManager(Request $request){
        $rules = [
            'manager_id' => 'required',
            'farm_id' => 'required',

        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }
        $user_id = Auth::user()->id;
        $farmmanager = new FarmManager();
        $farmmanager->manager_id = $request->manager_id;
        $farmmanager->user_id = $user_id;
        $farmmanager->farm_id = $data['farm_id'];
        $farmmanager->save();
        return [
            'status' => 'success',
            'message' => 'Manager assigned successfully',
            'data' => $farmmanager
        ];
    }
}
