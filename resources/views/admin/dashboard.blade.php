<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin - God Mode</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; background-color: #F3F4F6; } </style>
</head>
<body class="text-gray-800">

    <nav class="bg-[#1e293b] text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⚡</span>
                <div>
                    <h1 class="text-xl font-bold tracking-wider">GOD MODE</h1>
                    <p class="text-[10px] text-gray-400">Super Admin Dashboard</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <span class="text-sm text-gray-300">Halo, <b class="text-white">{{ $admin->username }}</b></span>
                
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-xs font-bold transition shadow-md">
                        LOGOUT
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 space-y-8">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
                <span class="text-3xl mb-2">👥</span>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Total Pengguna</p>
                <h3 class="text-4xl font-bold text-blue-600 mt-1">{{ $totalUsers }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
                <span class="text-3xl mb-2">🏪</span>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Total Toko</p>
                <h3 class="text-4xl font-bold text-purple-600 mt-1">{{ $totalUmkm }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
                <span class="text-3xl mb-2">🤖</span>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Prompt AI</p>
                <h3 class="text-4xl font-bold text-green-600 mt-1">{{ $totalPrompts }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
                <span class="text-3xl mb-2">💰</span>
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Omzet Global</p>
                <h3 class="text-2xl font-bold text-orange-600 mt-2">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div x-data="{ openSettings: false }" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 flex justify-between items-center bg-gray-50 border-b">
                <div>
                    <h3 class="font-bold text-lg text-gray-800">⚙️ Pengaturan Akun Admin</h3>
                    <p class="text-xs text-gray-500">Ganti username atau password admin di sini.</p>
                </div>
                <button @click="openSettings = !openSettings" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                    <span x-text="openSettings ? 'Tutup Panel' : 'Edit Akun'"></span>
                </button>
            </div>

            <div x-show="openSettings" x-transition class="p-8 bg-blue-50 border-b border-blue-100">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm font-bold border border-green-200">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Username Admin</label>
                        <input type="text" name="username" value="{{ $admin->username }}" class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Baru</label>
                        <input type="password" name="password" placeholder="(Biarkan kosong jika tetap)" class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-md w-full">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg text-gray-800">📡 Aktivitas Live (Siapa buka apa?)</h3>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full animate-pulse">● Realtime Logging</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3 rounded-l-lg">User / IP</th>
                                <th class="p-3">Halaman</th>
                                <th class="p-3 rounded-r-lg">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentActivities as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3">
                                    @if($log->name)
                                        <div class="font-bold text-blue-600 flex items-center gap-1">
                                            👤 {{ $log->name }}
                                        </div>
                                    @else
                                        <div class="font-bold text-orange-500 flex items-center gap-1">
                                            👻 Tamu (Guest)
                                        </div>
                                        <div class="text-[10px] text-gray-400">{{ $log->ip }}</div>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="text-gray-700 truncate max-w-xs" title="{{ $log->url }}">
                                        {{ Str::limit($log->url, 40) }}
                                    </div>
                                </td>
                                <td class="p-3 text-gray-400 text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                </td>
                            </tr>
                            @endforeach
                            @if(count($recentActivities) == 0)
                                <tr><td colspan="3" class="p-4 text-center text-gray-400">Belum ada aktivitas.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-slate-800 text-white p-6 rounded-2xl shadow-lg h-fit">
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                    <span>🤖</span> Raja Prompt AI
                </h3>
                <ul class="space-y-4">
                    @foreach($topAiUsers as $index => $stat)
                    <li class="flex items-center justify-between p-3 bg-white/10 rounded-xl hover:bg-white/20 transition cursor-default">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-yellow-400 text-black flex items-center justify-center font-bold">
                                #{{ $index + 1 }}
                            </div>
                            <span class="font-medium text-sm">{{ $stat->user->name }}</span>
                        </div>
                        <span class="text-xs font-bold bg-black/30 px-2 py-1 rounded">{{ $stat->total }} chat</span>
                    </li>
                    @endforeach
                    @if(count($topAiUsers) == 0)
                        <li class="text-gray-400 text-sm italic">Belum ada yang nge-chat AI.</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-lg mb-4 text-gray-800">📈 Grafik Kunjungan (7 Hari Terakhir)</h3>
            <div class="h-64">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('activityChart');
        // Data dari Controller
        const rawData = {!! json_encode($chartData) !!};
        
        new Chart(ctx, {
            type: 'bar', // Ganti ke 'line' jika mau garis
            data: {
                labels: rawData.map(d => d.date),
                datasets: [{
                    label: 'Total Aktivitas (Klik/Buka Halaman)',
                    data: rawData.map(d => d.total),
                    backgroundColor: '#3b82f6',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>