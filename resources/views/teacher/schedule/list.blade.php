@extends('layouts.app')

@section('content')

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

    <!-- Schedule Section -->
    <section class="mt-6">

    @include(' _message')
        <div class="bg-white bg-opacity-30 backdrop-blur-lg p-6 rounded-lg shadow-lg">
            <h1 class="text-lg font-karla font-semibold text-gray-900 mb-4">All Schedule</h2>

            <!-- Teacher Info -->
            <div class="flex items-center space-x-4 mb-4">
            <img src="{{  Auth::user()->getProfilePictureUrl() }}" 
            class="w-32 h-32 rounded-full object-cover border shadow-md" alt="Profile Picture">
                <div>
                    <h3 class="font-karla font-semibold">{{ Auth::user()->first_name }}</h3>
                    <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                </div>
                <span class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs">🟢 10hrs | Working Hours</span>
            </div>

            <!-- Calendar -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex space-x-2">
                    <select id="month-selector" class="p-2 border rounded-md bg-white">
                        @foreach (["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"] as $index => $month)
                            <option value="{{ $index }}">{{ $month }}</option>
                        @endforeach
                    </select>
                    <select id="year-selector" class="p-2 border rounded-md bg-white">
                        @for ($year = date('Y') - 5; $year <= date('Y') + 5; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <button id="openModalBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Create Schedule +
                </button>
            </div>

            <!-- Calendar -->
            <input type="hidden" id="schedule-data" value="{{ json_encode($schedules) }}">
            <div class="grid grid-cols-7 gap-4 mt-4 text-center">
                <div class="font-bold">Sun</div>
                <div class="font-bold">Mon</div>
                <div class="font-bold">Tue</div>
                <div class="font-bold">Wed</div>
                <div class="font-bold">Thu</div>
                <div class="font-bold">Fri</div>
                <div class="font-bold">Sat</div>
                <div id="calendar-grid" class="grid grid-cols-7 gap-4 col-span-7"></div>
            </div>
        </div>
    </section>

    <!-- Create Schedule Modal -->
    <div id="scheduleModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="w-full max-w-lg p-6 bg-white border-2 border-blue-600 rounded-lg shadow-lg">
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b border-blue-300 pb-2 mb-4">
                <h2 class="text-xl font-bold text-blue-700">CREATE SCHEDULE</h2>
                <button id="closeModalBtn" class="text-blue-700 hover:text-red-500 text-lg">&times;</button>
            </div>

            <!-- Modal Body -->
           <!-- Schedule Form -->
           <form action="{{ route('teacher.schedule.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    
                    <!-- Date Section -->
                    <div class="flex flex-col">
                        <label class="text-sm font-bold text-gray-700">Date</label>
                        <input type="date" name="date" id="scheduleDate" class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 w-full" required>

                    </div>

                    <!-- Time Section -->
                    <div class="flex flex-col">
                        <label class="text-sm font-bold text-gray-700">Time</label>
                        <div class="flex space-x-2">
                            <input type="time" name="start_time" class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:ring focus:ring-blue-300" required>
                            <input type="time" name="end_time" class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:ring focus:ring-blue-300" required>
                        </div>
                    </div>

                    <!-- Room Selection -->
                    <div class="flex flex-col">
                        <label class="text-sm font-bold text-gray-700">Room</label>
                        <select name="room_id" class="p-2 border border-gray-300 rounded-md bg-blue-600 text-white" required>
                            <option value="">Pick a room</option>
                            @foreach ($getRoom as $room)
                                <option value="{{ $room->id }}">{{ $room->room_name }} ({{ $room->capacity }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button id="closeModalBtn2" type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

</main>

<!-- JavaScript to Handle Datepicker & Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const openModalBtn = document.getElementById("openModalBtn");
        const closeModalBtn = document.getElementById("closeModalBtn");
        const closeModalBtn2 = document.getElementById("closeModalBtn2");
        const scheduleModal = document.getElementById("scheduleModal");

        openModalBtn.addEventListener("click", function () {
            scheduleModal.classList.remove("hidden");
        });

        closeModalBtn.addEventListener("click", function () {
            scheduleModal.classList.add("hidden");
        });

        closeModalBtn2.addEventListener("click", function () {
            scheduleModal.classList.add("hidden");
        });
    });
</script>

<!-- JavaScript for Calendar -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const calendarGrid = document.getElementById("calendar-grid");
        const scheduleData = JSON.parse(document.getElementById("schedule-data").value || "[]");
        const monthSelector = document.getElementById("month-selector");
        const yearSelector = document.getElementById("year-selector");

        function updateCalendar() {
            const month = parseInt(monthSelector.value);
            const year = parseInt(yearSelector.value);
            calendarGrid.innerHTML = "";

            const firstDay = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                calendarGrid.innerHTML += `<div></div>`;
            }

            for (let day = 1; day <= totalDays; day++) {
                let scheduleHTML = "";
                scheduleData.forEach(schedule => {
                    const scheduleDate = new Date(schedule.date);
                    if (scheduleDate.getFullYear() === year && scheduleDate.getMonth() === month && scheduleDate.getDate() === day) {
                        const startTime = formatTime(schedule.start_time);
                        const endTime = formatTime(schedule.end_time);
                        scheduleHTML += `
                       <div class="
                            text-white text-xs mt-2 p-1 rounded 
                            ${schedule.status === 'pending' ? 'bg-orange-500' : 
                            schedule.status === 'accepted' ? 'bg-green-500' : 
                            schedule.status === 'declined' ? 'bg-red-500' : 'bg-gray-500'}">
                            ${startTime} - ${endTime} (${schedule.room_name}) (${schedule.subject})
                        </div>

                    `;
                    }
                });
                calendarGrid.innerHTML += `<div class="bg-blue-700 text-white p-4 rounded-lg">${day}${scheduleHTML}</div>`;
            }
        }

        function formatTime(timeString) {
            const [hours, minutes] = timeString.split(':');
            let hoursInt = parseInt(hours);
            const ampm = hoursInt >= 12 ? 'PM' : 'AM';
            hoursInt = hoursInt % 12 || 12;
            return `${hoursInt}:${minutes} ${ampm}`;
        }

        monthSelector.addEventListener("change", updateCalendar);
        yearSelector.addEventListener("change", updateCalendar);

        monthSelector.value = new Date().getMonth();
        yearSelector.value = new Date().getFullYear();
        updateCalendar();
    });
</script>




@endsection


