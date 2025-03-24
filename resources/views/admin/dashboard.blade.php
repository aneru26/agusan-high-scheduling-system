@extends('layouts.app')

@section('content')

    <!-- Main Content -->
    <main class="relative z-10 flex-1 px-8 font-karla font-semibold">


        <!-- Top Bar -->
        <header style="background-color: #1E40AF;" class="flex justify-between items-center bg-blue-800 text-white px-6 py-4 rounded-lg shadow-md">
    <div class="flex items-center space-x-5">
        <i class="fa-solid fa-calendar-days text-lg"></i>
        <span id="current-date" class="text-sm">Loading date...</span>
        <span id="current-time" class="text-sm font-semibold">Loading time...</span>
    </div>
    <div class="flex items-center space-x-4">
        <span class="text-sm font-semibold">Welcome, {{ Auth::user()->first_name }}!</span>

        <!-- Notification Icon with Badge -->
        <div class="relative">
    <!-- Notification Bell Icon -->
    <i class="fa-solid fa-bell text-lg cursor-pointer text-white" id="notification-icon"></i>

    <!-- Notification Badge -->
    <span id="notification-badge"
        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full hidden">
        0
    </span>

    <!-- Notification Dropdown -->
    <div id="notification-dropdown"
        class="absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-lg z-50 hidden border border-gray-200">
        <div class="p-3">
            <p class="text-sm font-semibold text-gray-700">Notifications</p>
            <ul id="notification-list" class="mt-2 max-h-60 overflow-y-auto text-gray-800"></ul>
           
        </div>
    </div>
</div>
    </div>
</header>

        <!-- Upcoming Schedules -->
        <section class="mt-6">

        @include(' _message')

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">Manage Teacher Schedules</h2>
               
            </div>

            <!-- Schedule Table -->
            <div class="mt-4 overflow-x-auto">
            <table class="w-full bg-white bg-opacity-80 backdrop-blur-lg border border-white border-opacity-20 rounded-lg shadow-lg" id="myTable">
    <thead class="bg-blue-700 text-white ">
        <tr>
        <th class="py-3 px-4">Profile</th>
            <th class="py-3 px-4">Teacher</th>
            <th class="py-3 px-4">Subject</th>
            <th class="py-3 px-4">Room</th>
            <th class="py-3 px-4">Date</th>
            <th class="py-3 px-4">Time</th>
            <th class="py-3 px-4">Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($schedules as $schedule)
    <tr class="border-b border-gray-300 text-center">
    <td class="py-3 px-4 flex justify-center items-center">
    @if(!empty($schedule->teacher->getProfilePictureUrl()))
        <img src="{{ $schedule->teacher->getProfilePictureUrl() }}" class="h-12 w-12 rounded-full">
    @endif
</td>

    <td class="py-3 px-4">{{ optional($schedule->teacher)->first_name }}</td> 
    <td class="py-3 px-4">{{ optional($schedule->teacher)->subject }}</td> 
    <td class="py-3 px-4">{{ optional($schedule->room)->room_name }}</td> 
    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($schedule->date)->format('F j, Y') }}</td>

        <td class="py-3 px-4">
    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
</td>
        
<td class="py-3 px-4">
    <select name="status" 
        class="px-3 py-1 rounded border border-gray-300 text-white
            {{ $schedule->status == 'accepted' ? 'bg-green-500' :  
               ($schedule->status == 'declined' ? 'bg-red-500' :  
               ($schedule->status == 'completed' ? 'bg-gray-500' : 'bg-orange-500')) }}" 
        onchange="location = this.value;">
        
        <option disabled selected>{{ ucfirst($schedule->status) }}</option>
        <option value="{{ url('admin/schedule/accept/'.$schedule->id) }}">Accepted</option>
        <option value="{{ url('admin/schedule/decline/'.$schedule->id) }}">Declined</option>
        <option value="{{ url('admin/schedule/delete/'.$schedule->id) }}">Delete</option>
    </select>
</td>



    </tr>
@endforeach

    </tbody>
</table>

            </div>

            <div style="padding: 10px; float:right;" class="mt-4">
                {{ $schedules->links() }}
            </div>

        </section>
    </main>

    @endsection
