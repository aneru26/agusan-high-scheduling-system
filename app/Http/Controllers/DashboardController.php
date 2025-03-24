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

    return view('admin.dashboard', $data);
        
        } elseif (Auth::user()->user_type == 2) {

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

    
}
