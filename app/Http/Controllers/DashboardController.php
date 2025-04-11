<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Schedule;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['header_title'] = 'Dashboard';
    
        if (Auth::user()->user_type == 1) {
          
        Schedule::updateCompletedSchedules(); // Call function from model

    $data['schedules'] = Schedule::with(['room', 'teacher'])
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->paginate(5);

    return view('admin.schedules.AllList', $data);
        
        } elseif (Auth::user()->user_type == 2) {


            Schedule::updateCompletedSchedules(); // Call function from model
            
            $data['schedules'] = Schedule::with(['room', 'teacher'])
            ->where('teacher_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(5);
        
        return view('teacher.dashboard', $data);
        

        } elseif (Auth::user()->user_type == 3) {

            return view('inspector.dashboard', $data);
        }
    }


    public function upcoming()
    {
        $data['header_title'] = 'Dashboard';
    
        (Auth::user()->user_type == 1);
          
        Schedule::updateCompletedSchedules(); // Call function from model

    $data['schedules'] = Schedule::with(['room', 'teacher'])
        ->where('status', 'upcoming')
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->paginate(5);

    return view('admin.schedules.upcoming', $data);    
    
}


public function ongoing()
    {
        $data['header_title'] = 'Dashboard';
    
        (Auth::user()->user_type == 1);
          
        Schedule::updateCompletedSchedules(); // Call function from model

    $data['schedules'] = Schedule::with(['room', 'teacher'])
        ->where('status', 'ongoing')
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->paginate(5);

    return view('admin.schedules.ongoing', $data);    
    
}


public function completed()
    {
        $data['header_title'] = 'Dashboard';
    
        (Auth::user()->user_type == 1);
          
        Schedule::updateCompletedSchedules(); // Call function from model

    $data['schedules'] = Schedule::with(['room', 'teacher'])
        ->where('status', 'completed')
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->paginate(5);

    return view('admin.schedules.completed', $data);    
    
}


public function declined()
    {
        $data['header_title'] = 'Dashboard';
    
        (Auth::user()->user_type == 1);
          
        Schedule::updateCompletedSchedules(); // Call function from model

    $data['schedules'] = Schedule::with(['room', 'teacher'])
        ->where('status', 'declined')
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->paginate(5);

    return view('admin.schedules.declined', $data);    
    
}

}
