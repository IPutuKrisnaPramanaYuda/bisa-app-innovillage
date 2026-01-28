<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login Admin - BISA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body class="bg-[#0F244A] flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Admin Panel 🔒</h1>
            <p class="text-gray-500 text-sm">Masuk untuk mengelola sistem</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm font-bold text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.authenticate') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="admin">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="••••••">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition">
                Masuk
            </button>
        </form>
    </div>
</body>
</html>