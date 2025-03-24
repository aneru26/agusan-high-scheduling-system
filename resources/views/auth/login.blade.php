<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agusan National High School</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-blue-100 flex flex-col items-center justify-center min-h-screen">

   <!-- Background Image -->
   <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-sm"
     @style("background-image: url(" . asset('asset/img/background_image.png') . ");")>
</div>


    <!-- Overlay -->
    <div class="absolute inset-0 bg-blue-900 bg-opacity-50"></div>

      <!-- Header -->
      <header class="absolute top-4 left-4 right-4 flex justify-between items-center p-3 bg-primary text-white rounded-lg shadow-lg w-[95%] md:w-[80%] mx-auto">
        <div class="flex items-center space-x-5">
            <i class="fa-solid fa-calendar-days text-lg"></i>
            <span id="current-date" class="text-sm">Loading date...</span>
            <span id="current-time" class="text-sm font-semibold">Loading time...</span>
        </div>
    </header>

    <!-- Content -->
    <div class="relative z-10 flex flex-col items-center w-full max-w-lg px-6 py-12 
            bg-white bg-opacity-30 backdrop-blur-lg border border-white border-opacity-20 
            rounded-lg shadow-lg mt-20">

            <img src="{{ asset('asset/img/logo.png') }}" alt="School Logo" class="w-24 mb-4">
        
        <h1 class="text-2xl font-semibold text-gray-800 text-center">Agusan National High School</h1>
        <p class="text-sm text-gray-600 text-center mb-6">Butuan City, Agusan Del Norte, Philippines</p>

        <!-- Login Form -->
        @include(' _message')
        <form class="w-full" action="{{ url('login') }}" method="post">
        {{ csrf_field() }}
            <div class="mb-4">
                <input type="email" name="email" required placeholder="Enter email" class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <input type="password" name="password" required placeholder="Enter password" class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-center mb-4">
                <input type="checkbox" id="remember" name="remember" class="mr-2">
                <label for="remember" class="text-sm text-gray-700">Remember email</label>
            </div>
            <button class="w-full py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-md">Log in</button>
        </form>

        <p class="mt-4 text-sm text-gray-600">Don't have an account? <a href="{{ url('register')}}" class="text-white hover:underline">Sign up</a></p>
    </div>


     <!-- JavaScript for Real-Time Date and Time -->
     <script>
        function updateTime() {
            const now = new Date();
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            document.getElementById('current-date').innerText = now.toLocaleDateString('en-US', options);
            document.getElementById('current-time').innerText = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>


</body>
</html>
