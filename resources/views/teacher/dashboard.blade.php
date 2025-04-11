@extends('layouts.app')

@section('content')

<!-- Main Content -->
<main class="relative z-10 flex-1 px-4 sm:px-6 md:px-8 font-karla font-semibold">

    <!-- Top Bar -->
    <header class="flex flex-col sm:flex-row justify-between items-center bg-white text-gray-700  px-4 sm:px-6 py-4 rounded-lg shadow-md space-y-2 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <i class="fa-solid fa-calendar-days text-lg"></i>
            <span id="current-date" class="text-sm">Loading date...</span>
            <span id="current-time" class="text-sm font-semibold">Loading time...</span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm font-semibold">Welcome, {{ Auth::user()->first_name }}!</span>

            <!-- Notification Icon with Badge -->
            <div class="relative">
    <i class="fa-solid fa-bell text-lg cursor-pointer text-gray-700" id="notification-icon"></i>
    <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full hidden">0</span>

    <!-- Notification Dropdown -->
    <div id="notification-dropdown" class="absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-lg z-50 hidden border border-gray-200">
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

        @include('_message')

        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-700 ">Manage Teacher Schedules</h2>
        </div>

        <!-- Schedule Table -->
        <div class="mt-4">
            <!-- Scrollable Table for small screens -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white bg-opacity-80 backdrop-blur-lg border border-white border-opacity-20 rounded-lg shadow-lg text-sm sm:text-base">
                <thead class="bg-white text-gray-700 shadow border-b border-gray-300">

                        <tr>
                            <th class="py-3 px-4">Room</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Time</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Remarks</th>
                            <th class="py-3 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr class="border-b border-gray-300 text-center hidden sm:table-row"> 
                                <td class="py-3 px-4">{{ optional($schedule->room)->room_name }}</td> 
                                <td class="py-3 px-4">{{ \Carbon\Carbon::parse($schedule->date)->format('F j, Y') }}</td>

                                <td class="py-3 px-4">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 rounded text-white text-xs sm:text-sm 
                                        {{ $schedule->status == 'upcoming' ? 'bg-green-500' : ($schedule->status == 'ongoing' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ ($schedule->remarks) }}</td> 
                                <td class="py-3 px-4">
                                    <a href="{{ url('teacher/schedule/delete/'.$schedule->id) }}" 
                                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-xs sm:text-sm"
                                        onclick="return confirm('Are you sure you want to delete this room?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Mobile View: Card Layout -->
                            <tr class="sm:hidden">
    <td colspan="5" class="p-4">
        <div class="bg-white p-3 rounded-lg shadow">
            <p><strong>Room:</strong> {{ optional($schedule->room)->room_name }}</p>
            <p><strong>Date:</strong> {{ $schedule->date }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</p>
            <p><span class="px-3 py-1 rounded text-white">{{ ucfirst($schedule->status) }}</span></p>
            <div>
                <a href="{{ url('teacher/schedule/delete/'.$schedule->id) }}" class="bg-red-600 text-white">Delete</a>
            </div>
        </div>
    </td>
</tr>


                        @endforeach
                    </tbody>
                </table>

                
            </div>
            <div style="padding: 10px; float:right;" class="mt-4">
                {{ $schedules->links() }}
            </div>
            
        </div>

    </section>
</main>

@endsection
