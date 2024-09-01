<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function schedule(Request $request){
        $request->validate([
            'venue'=>'required',
            'training_date'=>'required',
            'instructor'=>'required',
            'category'=>'required',
        ]);

        $schedule = new Schedule();
        $data=$request->all();
        $schedule->fill($data);
        $schedule->save();

        return response()->json([
            'status'=>'success',
            'schedule'=>$schedule,
            'message' =>'Schedule added successfully'
        ]);
    }

    public function listSchedules(){
        $schedules = Schedule::all();
        return response()->json([
            'status'=>'success',
            'schedules'=>$schedules,
            'message' =>'Schedule listed successfully'
        ]);
    }

}
