<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use App\Models\User;

class ScheduleController extends Controller
{

    //teacher
    public function schedulelist()
    {
        $userSubject = Auth::user()->subject; // Get the logged-in user's subject

    // Fetch only the rooms where the room subject matches the user's subject
         $getRoom = Room::where('subject', $userSubject)->get();
    
        // Fetch only accepted schedules, including related teacher and room
        $schedules = Schedule::with(['room', 'teacher'])
        ->whereIn('status', ['accepted', 'completed'])  // Filter schedules with status "accepted"
        ->whereHas('teacher', function ($query) use ($userSubject) {
            $query->where('subject', $userSubject); // Ensure the schedule's teacher has the same subject
        })
            ->get()
            ->map(function ($schedule) {
                return [
                    'date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'status' => $schedule->status,
                    'room_name' => optional($schedule->room)->room_name, // Avoid null errors
                    'subject' => optional($schedule->teacher)->subject,
                ];
            });
    
        return view('teacher.schedule.list', compact('getRoom', 'schedules'));
    }
    
    

    

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|exists:rooms,id',
        ]);
    
        // Get available slots using greedy algorithm
        $availableSlots = $this->GreedyAlgorithm($request->date, $request->room_id);
    
        // Check if requested time is available
        $isAvailable = false;
        foreach ($availableSlots as $slot) {
            if ($request->start_time >= $slot[0] && $request->end_time <= $slot[1]) {
                $isAvailable = true;
                break;
            }
        }
    
        if (!$isAvailable) {
            return redirect()->back()->with('error', 'The selected time slot is unavailable. Choose another time.');
        }
    
        // Save the new schedule if valid
        $schedule = Schedule::create([
            'teacher_id' => Auth::id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room_id' => $request->room_id,
        ]);
    
        // Convert start and end times to 12-hour format
        $formattedStartTime = date('h:i A', strtotime($schedule->start_time));
        $formattedEndTime = date('h:i A', strtotime($schedule->end_time));
    
        // Notify Admin
        $admins = User::where('user_type', '1')->get(); // Assuming '1' is the admin user type
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'message' => "A new schedule has been requested by " . Auth::user()->first_name .
                    " on {$schedule->date} from {$formattedStartTime} to {$formattedEndTime}.",
            ]);
        }
    
        return redirect()->back()->with('success', 'Schedule created successfully!');
    }
    

  
    private function GreedyAlgorithm($date, $roomId)
{
    // Fetch existing schedules for the given date and room
    $existingSchedules = Schedule::where('date', $date)
        ->where('room_id', $roomId)
        ->orderBy('start_time')
        ->get();

    // Initialize available time slots (Assume 8 AM to 6 PM as the full range)
    $availableSlots = [['00:00:00', '23:59:59']];

    foreach ($existingSchedules as $schedule) {
        $newSlots = [];

        foreach ($availableSlots as $slot) {
            [$slotStart, $slotEnd] = $slot;

            // If the scheduled time conflicts, adjust available slots
            if ($schedule->start_time > $slotStart) {
                $newSlots[] = [$slotStart, $schedule->start_time];
            }

            if ($schedule->end_time < $slotEnd) {
                $newSlots[] = [$schedule->end_time, $slotEnd];
            }
        }

        $availableSlots = $newSlots;
    }

    return $availableSlots;
}

    //admin


    public function adminschedulelist()
    {
        $getRoom = Room::all(); // Fetch all rooms
        $schedules = Schedule::with(['room', 'teacher']) // Load room and teacher details
        ->whereIn('status', ['accepted', 'completed'])
            ->get()
            ->map(function ($schedule) {
                return [
                    'teacher_name' => optional($schedule->teacher)->first_name, // Get teacher name
                    'subject' => optional($schedule->teacher)->subject,
                    'date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'status' => $schedule->status,
                    'room_name' => optional($schedule->room)->room_name, // Get room name safely
                ];
            });
    
        return view('admin.schedule.list', compact('getRoom', 'schedules'));
    }
    

    public function accept(Request $request, $id)
{
    $data = Schedule::findOrFail($id);
    $data->status = 'accepted';
    $data->save();

    // Convert start and end times to 12-hour format
    $start_time = date('h:i A', strtotime($data->start_time));
    $end_time = date('h:i A', strtotime($data->end_time));

    // Store notification for the teacher
    Notification::create([
        'user_id' => $data->teacher_id,
        'message' => "Your schedule on {$data->date} from {$start_time} to {$end_time} has been accepted.",
    ]);

    return redirect()->back()->with('success', "Schedule Accepted");
}

    
    

public function decline(Request $request, $id)
{
    $data = Schedule::findOrFail($id);
    $data->status = 'declined';
    $data->save();

    // Convert start and end times to 12-hour format
    $start_time = date('h:i A', strtotime($data->start_time));
    $end_time = date('h:i A', strtotime($data->end_time));

    // Store notification for the teacher
    Notification::create([
        'user_id' => $data->teacher_id,
        'message' => "Your schedule on {$data->date} from {$start_time} to {$end_time} has been declined.",
    ]);

    return redirect()->back()->with('error', "Schedule Declined");
}



    public function delete($id)
    {
        $data = Schedule::findOrFail($id);
        $data->delete(); 
    
        return redirect()->back()->with('success', "Schedule Successfully Deleted");
    }


    public function getNotifications()
{
    $notifications = Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($notifications);
}


public function markNotificationsAsRead()
{
    Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['success' => true]);
}


    
}
