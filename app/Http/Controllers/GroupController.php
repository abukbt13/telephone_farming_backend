<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
   public function createGroup(GroupRequest $request){
       $data = $request->all();

       $user_id = Auth::user()->id;

       $group = new Group();
       $group['user_id'] = $user_id;
       $group->fill($data);
       $group->save();

       return response()->json([
           'status' => 'success',
           'message' => 'group created successfully',
           'group' => $group
       ]);
   }

public function listGroup(){

       $user_id = Auth::user()->id;

       $group = Group::where('user_id', $user_id)->get();

       return response()->json([
           'status' => 'success',
           'message' => 'group created successfully',
           'groups' => $group
       ]);
   }
}
