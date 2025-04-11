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
        $user = Auth::user();
    
        // Fetch rooms related to the teacher's subject
        $getRoom = Room::where('subject', $user->subject)->get();
    
        // Fetch schedules created by the logged-in teacher
        $schedules = Schedule::with(['room', 'teacher'])
            ->whereIn('status', ['upcoming', 'completed', 'ongoing'])
            ->where('teacher_id', $user->id)
            ->get()
            ->map(function ($schedule) {
                return [
                    'date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'status' => $schedule->status,
                    'room_id' => $schedule->room_id,
                    'room_name' => optional($schedule->room)->room_name,
                    'subject' => optional($schedule->teacher)->subject,
                ];
            });
    
        // Fetch all upcoming or ongoing schedules in rooms that match the teacher's subject
        $allRoomSchedules = Schedule::with(['room', 'teacher'])
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->whereHas('room', function ($query) use ($user) {
                $query->where('subject', $user->subject);
            })
            ->get()
            ->map(function ($schedule) {
                return [
                    'room_id' => $schedule->room_id,
                    'date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ];
            });
    
        // Pass all needed data to the view
        return view('teacher.schedule.list', compact('getRoom', 'schedules', 'allRoomSchedules'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|exists:rooms,id',
        ]);
    
    
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
        ->whereIn('status', ['upcoming', 'completed' , 'ongoing'])
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
    $request->validate([
        'remarks' => 'required|string|max:255'
    ]);

    $data = Schedule::findOrFail($id);
    $data->status = 'declined';
    $data->remarks = $request->remarks;
    $data->save();

    // Convert times
    $start_time = date('h:i A', strtotime($data->start_time));
    $end_time = date('h:i A', strtotime($data->end_time));

    // Notification
    Notification::create([
        'user_id' => $data->teacher_id,
        'message' => "Your schedule on {$data->date} from {$start_time} to {$end_time} has been declined. Reason: {$request->remarks}",
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


public function updateOngoingSchedules()
{
    $now = Carbon::now('Asia/Manila');
    $now = Carbon::now()->format('H:i:s'); // Get current time in HH:MM:SS format

    // Find schedules where the start_time matches the current time and status is 'upcoming'
    $schedules = Schedule::where('start_time', $now)
        ->where('status', 'upcoming')
        ->get();

    foreach ($schedules as $schedule) {
        $schedule->status = 'ongoing';
        $schedule->save();

        // Notify the teacher
        Notification::create([
            'user_id' => $schedule->teacher_id,
            'message' => "Your schedule on {$schedule->date} at {$schedule->start_time} has started and is now ongoing.",
        ]);
    }

    return response()->json(['message' => 'Ongoing schedules updated successfully']);
}



public function markNotificationsAsRead()
{
    Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['success' => true]);
}


    
}
