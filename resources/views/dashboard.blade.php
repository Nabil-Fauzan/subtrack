<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SubTrack - Recurring Cost & Subscription Analyzer</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        dark: {
                            950: '#09090b',
                            900: '#111318',
                            850: '#16181f',
                            800: '#1e212b',
                            700: '#2b2f3e',
                            600: '#3f455b',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #09090b;
            color: #f4f4f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #09090b;
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }
    </style>
</head>
<body class="min-h-screen bg-[#09090b] text-zinc-100 antialiased selection:bg-indigo-500 selection:text-white" x-data="{ 
    modalOpen: false, 
    filterCategory: 'all', 
    filterCycle: 'all',
    filterStatus: 'all',
    searchQuery: '',
    confirmDeleteId: null,
    confirmDeleteName: ''
}">

    <!-- Top Navigation / Header -->
    <header class="border-b border-zinc-800/80 bg-zinc-950/70 backdrop-blur-xl sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 via-violet-600 to-emerald-400 p-[1px] shadow-lg shadow-indigo-500/20">
                    <div class="w-full h-full bg-[#09090b] rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-center space-x-2.5">
                        <h1 class="text-xl font-bold tracking-tight text-white">SubTrack</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold uppercase tracking-wider rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Cost Analyzer</span>
                    </div>
                    <p class="text-xs text-zinc-400 hidden sm:block">Recurring Cost & Subscription Intelligence Dashboard</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button @click="modalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 transition-all duration-200 cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Langganan</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Flash Alert -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms class="p-4 rounded-2xl bg-emerald-950/50 border border-emerald-500/30 flex items-center justify-between text-emerald-300 shadow-lg shadow-emerald-950/40">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl bg-rose-950/50 border border-rose-500/30 text-rose-300 shadow-lg shadow-rose-950/40">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-400 flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-rose-200">Terjadi kesalahan validasi:</h4>
                        <ul class="list-disc list-inside text-xs mt-1 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Top Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Monthly Spend -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-zinc-900/90 to-zinc-950/90 border border-zinc-800/80 p-6 shadow-xl hover:border-zinc-700/60 transition-all duration-300 group">
                <div class="absolute -right-6 -top-6 w-28 h-28 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Total Monthly Cost</span>
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight">
                        Rp {{ number_format($metrics['total_monthly_cost'], 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-zinc-400">Normalisasi pengeluaran bulanan aktif</p>
                </div>
                <div class="mt-4 pt-3 border-t border-zinc-800/60 flex items-center justify-between text-xs text-zinc-400">
                    <span>Estimasi Harian</span>
                    <span class="font-semibold text-zinc-300">Rp {{ number_format($metrics['total_monthly_cost'] / 30, 0, ',', '.') }}/hari</span>
                </div>
            </div>

            <!-- Projected Yearly Spend -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-zinc-900/90 to-zinc-950/90 border border-zinc-800/80 p-6 shadow-xl hover:border-zinc-700/60 transition-all duration-300 group">
                <div class="absolute -right-6 -top-6 w-28 h-28 bg-violet-500/10 rounded-full blur-2xl group-hover:bg-violet-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Projected Yearly Cost</span>
                    <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight">
                        Rp {{ number_format($metrics['total_yearly_cost'], 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-zinc-400">Proyeksi beban biaya per 12 bulan</p>
                </div>
                <div class="mt-4 pt-3 border-t border-zinc-800/60 flex items-center justify-between text-xs text-zinc-400">
                    <span>Rata-rata per layanan</span>
                    <span class="font-semibold text-zinc-300">
                        @if($metrics['active_count'] > 0)
                            Rp {{ number_format($metrics['total_monthly_cost'] / $metrics['active_count'], 0, ',', '.') }}/bln
                        @else
                            Rp 0
                        @endif
                    </span>
                </div>
            </div>

            <!-- Active vs Inactive Subscriptions -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-zinc-900/90 to-zinc-950/90 border border-zinc-800/80 p-6 shadow-xl hover:border-zinc-700/60 transition-all duration-300 group">
                <div class="absolute -right-6 -top-6 w-28 h-28 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Subscription Status</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex items-baseline space-x-2">
                        <span class="text-2xl lg:text-3xl font-extrabold text-emerald-400">{{ $metrics['active_count'] }}</span>
                        <span class="text-sm font-medium text-zinc-400">Aktif</span>
                        <span class="text-zinc-600">/</span>
                        <span class="text-lg font-bold text-zinc-400">{{ $metrics['inactive_count'] }}</span>
                        <span class="text-xs text-zinc-500">Nonaktif</span>
                    </div>
                    <p class="text-xs text-zinc-400">Total {{ $subscriptions->count() }} layanan terdaftar</p>
                </div>
                <div class="mt-4 pt-3 border-t border-zinc-800/60">
                    <div class="w-full bg-zinc-800 rounded-full h-2 overflow-hidden flex">
                        @php
                            $totalSubs = $subscriptions->count();
                            $activePercent = $totalSubs > 0 ? ($metrics['active_count'] / $totalSubs) * 100 : 0;
                        @endphp
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $activePercent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Renewals & Overdue Alert -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-zinc-900/90 to-zinc-950/90 border border-zinc-800/80 p-6 shadow-xl hover:border-zinc-700/60 transition-all duration-300 group">
                <div class="absolute -right-6 -top-6 w-28 h-28 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Billing Alerts</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-baseline space-x-1.5">
                            <span class="text-2xl lg:text-3xl font-extrabold text-amber-400">{{ $metrics['renewing_soon_count'] }}</span>
                            <span class="text-xs font-medium text-amber-300/80">Segera (&le;7hr)</span>
                        </div>
                        @if($metrics['overdue_count'] > 0)
                            <div class="flex items-baseline space-x-1 px-2 py-0.5 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-400">
                                <span class="text-xs font-bold">{{ $metrics['overdue_count'] }}</span>
                                <span class="text-[10px] uppercase">Lewat</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-400">Perhatian perpanjangan langganan</p>
                </div>
                <div class="mt-4 pt-3 border-t border-zinc-800/60 flex items-center justify-between text-xs">
                    <span class="text-zinc-400">Status Perpanjangan</span>
                    @if($metrics['renewing_soon_count'] > 0 || $metrics['overdue_count'] > 0)
                        <span class="font-medium text-amber-400 flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                            <span>Perlu Tindakan</span>
                        </span>
                    @else
                        <span class="font-medium text-emerald-400">Aman & Terkendali</span>
                    @endif
                </div>
            </div>

        </div>

        <!-- Visual Stacked Bar: Category Cost Breakdown -->
        <div class="rounded-3xl bg-zinc-900/60 border border-zinc-800/80 p-6 sm:p-7 shadow-xl backdrop-blur-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 gap-4">
                <div>
                    <h2 class="text-base font-bold text-white tracking-tight flex items-center space-x-2">
                        <span>Alokasi Pengeluaran per Kategori</span>
                    </h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Proporsi pengeluaran bulanan berdasarkan kategori langganan aktif</p>
                </div>
                <div class="text-right sm:text-left">
                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Total Terdistribusi:</span>
                    <span class="text-sm font-bold text-indigo-400 ml-1">Rp {{ number_format($metrics['total_monthly_cost'], 0, ',', '.') }}/bln</span>
                </div>
            </div>

            <!-- Multi-Color Segmented Bar -->
            <div class="w-full h-4 bg-zinc-950 rounded-full overflow-hidden flex p-0.5 border border-zinc-800">
                @forelse($categoryBreakdown as $cat)
                    <div 
                        class="h-full first:rounded-l-full last:rounded-r-full transition-all duration-500 hover:brightness-125 cursor-pointer relative group"
                        style="width: {{ $cat['percentage'] }}%; background-color: {{ $cat['color_hex'] }};"
                        title="{{ $cat['name'] }}: {{ $cat['percentage'] }}% (Rp {{ number_format($cat['monthly_cost'], 0, ',', '.') }})"
                    ></div>
                @empty
                    <div class="w-full h-full bg-zinc-800 rounded-full"></div>
                @endforelse
            </div>

            <!-- Category Legend Items -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-4 border-t border-zinc-800/60">
                @forelse($categoryBreakdown as $cat)
                    <div class="flex items-start space-x-3 p-2.5 rounded-2xl bg-zinc-950/40 border border-zinc-800/40 hover:border-zinc-700/60 transition-all">
                        <div class="w-3.5 h-3.5 rounded-full mt-1 flex-shrink-0 shadow-sm" style="background-color: {{ $cat['color_hex'] }};"></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-zinc-200 truncate">{{ $cat['name'] }}</p>
                                <span class="text-[11px] font-bold text-zinc-400 ml-1">{{ $cat['percentage'] }}%</span>
                            </div>
                            <p class="text-xs font-medium text-zinc-400 mt-0.5">Rp {{ number_format($cat['monthly_cost'], 0, ',', '.') }}</p>
                            <p class="text-[10px] text-zinc-500">{{ $cat['active_count'] }} aktif</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-2 text-xs text-zinc-500">
                        Belum ada data pengeluaran kategori aktif.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Subscription Management Section -->
        <div class="rounded-3xl bg-zinc-900/60 border border-zinc-800/80 shadow-xl overflow-hidden backdrop-blur-md">
            
            <!-- Filter & Search Bar -->
            <div class="p-6 border-b border-zinc-800/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight">Daftar Langganan</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Kelola dan pantau seluruh siklus langganan Anda secara realtime</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative min-w-[200px] flex-1 sm:flex-initial">
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            placeholder="Cari nama layanan..." 
                            class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-zinc-950/80 border border-zinc-800 text-zinc-200 placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        >
                        <svg class="w-4 h-4 text-zinc-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Category Filter -->
                    <select 
                        x-model="filterCategory" 
                        class="px-3 py-2 text-xs rounded-xl bg-zinc-950/80 border border-zinc-800 text-zinc-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <!-- Cycle Filter -->
                    <select 
                        x-model="filterCycle" 
                        class="px-3 py-2 text-xs rounded-xl bg-zinc-950/80 border border-zinc-800 text-zinc-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="all">Semua Siklus</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>

                    <!-- Status Filter -->
                    <select 
                        x-model="filterStatus" 
                        class="px-3 py-2 text-xs rounded-xl bg-zinc-950/80 border border-zinc-800 text-zinc-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="all">Semua Status</option>
                        <option value="active">Hanya Aktif</option>
                        <option value="inactive">Hanya Nonaktif</option>
                        <option value="renewing_soon">Segera Perpanjang (&le;7hr)</option>
                    </select>
                </div>
            </div>

            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800/80 bg-zinc-950/40 text-[11px] uppercase tracking-wider font-semibold text-zinc-400">
                            <th class="py-4 px-6">Layanan</th>
                            <th class="py-4 px-4">Kategori</th>
                            <th class="py-4 px-4">Biaya & Siklus</th>
                            <th class="py-4 px-4">Normalisasi Bulanan</th>
                            <th class="py-4 px-4">Next Billing</th>
                            <th class="py-4 px-4">Metode Bayar</th>
                            <th class="py-4 px-4 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60 text-sm">
                        @forelse($subscriptions as $sub)
                            @php
                                $daysDiff = $sub->days_until_renewal;
                            @endphp
                            <tr 
                                class="hover:bg-zinc-800/30 transition-colors {{ !$sub->is_active ? 'opacity-50 hover:opacity-80' : '' }}"
                                x-show="
                                    (filterCategory === 'all' || filterCategory == '{{ $sub->category_id }}') &&
                                    (filterCycle === 'all' || filterCycle == '{{ $sub->billing_cycle }}') &&
                                    (
                                        filterStatus === 'all' || 
                                        (filterStatus === 'active' && {{ $sub->is_active ? 'true' : 'false' }}) ||
                                        (filterStatus === 'inactive' && !{{ $sub->is_active ? 'true' : 'false' }}) ||
                                        (filterStatus === 'renewing_soon' && {{ $sub->is_renewing_soon ? 'true' : 'false' }})
                                    ) &&
                                    ('{{ strtolower($sub->service_name) }}'.includes(searchQuery.toLowerCase()))
                                "
                            >
                                <!-- Service Name & Icon -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-3.5">
                                        <div 
                                            class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm shadow-md" 
                                            style="background: {{ $sub->category->color_hex }}1a; color: {{ $sub->category->color_hex }}; border: 1px solid {{ $sub->category->color_hex }}40;"
                                        >
                                            {{ strtoupper(substr($sub->service_name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-zinc-100 flex items-center space-x-2">
                                                <span>{{ $sub->service_name }}</span>
                                            </div>
                                            <span class="text-xs text-zinc-500">ID #{{ $sub->id }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category Badge -->
                                <td class="py-4 px-4">
                                    <span 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                        style="background-color: {{ $sub->category->color_hex }}15; color: {{ $sub->category->color_hex }}; border: 1px solid {{ $sub->category->color_hex }}40;"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $sub->category->color_hex }};"></span>
                                        {{ $sub->category->name }}
                                    </span>
                                </td>

                                <!-- Price & Cycle -->
                                <td class="py-4 px-4">
                                    <div class="font-bold text-zinc-200">
                                        Rp {{ number_format($sub->price, 0, ',', '.') }}
                                    </div>
                                    <span class="inline-block text-[11px] font-medium text-zinc-400 capitalize">
                                        / {{ $sub->billing_cycle }}
                                    </span>
                                </td>

                                <!-- Normalized Monthly Cost -->
                                <td class="py-4 px-4">
                                    <div class="text-sm font-semibold text-indigo-300">
                                        Rp {{ number_format($sub->normalized_monthly_cost, 0, ',', '.') }}
                                    </div>
                                    <span class="text-[10px] text-zinc-500">per bulan</span>
                                </td>

                                <!-- Next Billing Date & Renewal Status -->
                                <td class="py-4 px-4">
                                    <div class="text-xs font-medium text-zinc-300">
                                        {{ \Carbon\Carbon::parse($sub->next_billing_date)->format('d M Y') }}
                                    </div>
                                    <div class="mt-1">
                                        @if($sub->is_overdue)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                                ⚠️ Terlewat (Overdue)
                                            </span>
                                        @elseif($sub->is_renewing_soon)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse">
                                                ⏳ Perpanjang {{ $daysDiff == 0 ? 'Hari ini' : 'dalam ' . $daysDiff . ' hari' }}
                                            </span>
                                        @else
                                            <span class="text-[11px] text-zinc-500">
                                                {{ $daysDiff }} hari lagi
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Payment Method -->
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-800/80 text-zinc-300 border border-zinc-700/60">
                                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span>{{ $sub->paymentMethod->name }}</span>
                                    </span>
                                </td>

                                <!-- Toggle Active Status -->
                                <td class="py-4 px-4 text-center">
                                    <form action="{{ route('subscriptions.toggle', $sub->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            title="Klik untuk ubah status"
                                            class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer {{ $sub->is_active ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-zinc-800 text-zinc-400 border border-zinc-700 hover:bg-zinc-700' }}"
                                        >
                                            <span class="w-2 h-2 rounded-full {{ $sub->is_active ? 'bg-emerald-400' : 'bg-zinc-500' }}"></span>
                                            <span>{{ $sub->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-right">
                                    <form action="{{ route('subscriptions.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus langganan {{ $sub->service_name }}?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-2 rounded-xl text-zinc-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                                            title="Hapus Langganan"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-zinc-500">
                                    <svg class="w-12 h-12 mx-auto text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <p class="text-base font-semibold text-zinc-400">Belum ada data langganan</p>
                                    <p class="text-xs text-zinc-500 mt-1">Klik tombol "Tambah Langganan" untuk mencatat recurring cost pertama Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Stats -->
            <div class="p-4 border-t border-zinc-800/80 bg-zinc-950/40 flex flex-col sm:flex-row items-center justify-between text-xs text-zinc-400 gap-2">
                <span>Menampilkan {{ $subscriptions->count() }} total langganan</span>
                <span class="text-zinc-500">SubTrack System v1.0 &bull; Auto Normalized Currency IDR</span>
            </div>
        </div>

    </main>

    <!-- Modal: Add New Subscription -->
    <div 
        x-show="modalOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.outside="modalOpen = false"
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-xl rounded-3xl bg-[#121318] border border-zinc-800 shadow-2xl p-6 sm:p-8 text-zinc-100 overflow-hidden"
        >
            <div class="absolute -right-12 -top-12 w-40 h-40 bg-indigo-500/15 rounded-full blur-3xl"></div>

            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-5 border-b border-zinc-800/80">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Tambah Langganan Baru</h3>
                        <p class="text-xs text-zinc-400">Masukkan detail recurring cost untuk dianalisis</p>
                    </div>
                </div>
                <button @click="modalOpen = false" class="p-2 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('subscriptions.store') }}" method="POST" class="mt-6 space-y-5">
                @csrf

                <!-- Service Name -->
                <div>
                    <label for="service_name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-1.5">
                        Nama Layanan / Tool <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="service_name" 
                        name="service_name" 
                        required 
                        placeholder="Contoh: GitHub Copilot, Spotify, ChatGPT Plus"
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                    >
                </div>

                <!-- Category & Payment Method Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-1.5">
                            Kategori <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            id="category_id" 
                            name="category_id" 
                            required
                            class="w-full px-4 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                        >
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-1.5">
                            Metode Pembayaran <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            id="payment_method_id" 
                            name="payment_method_id" 
                            required
                            class="w-full px-4 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                        >
                            <option value="" disabled selected>Pilih Metode Bayar</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Price & Billing Cycle Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Price (IDR) -->
                    <div>
                        <label for="price" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-1.5">
                            Harga (IDR) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-xs font-bold text-zinc-400">Rp</span>
                            <input 
                                type="number" 
                                step="any"
                                min="0" 
                                id="price" 
                                name="price" 
                                required 
                                placeholder="150000"
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                            >
                        </div>
                    </div>

                    <!-- Billing Cycle -->
                    <div>
                        <label for="billing_cycle" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-1.5">
                            Siklus Penagihan <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            id="billing_cycle" 
                            name="billing_cycle" 
                            required
                            class="w-full px-4 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                        >
                            <option value="monthly" selected>Monthly (Bulanan)</option>
                            <option value="quarterly">Quarterly (Per 3 Bulan)</option>
                            <option value="yearly">Yearly (Tahunan)</option>
                        </select>
                    </div>
                </div>

                <!-- Next Billing Date & Active Toggle -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <!-- Next Billing Date -->
                    <div>
                        <label for="next_billing_date" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-1.5">
                            Next Billing Date <span class="text-rose-400">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="next_billing_date" 
                            name="next_billing_date" 
                            required
                            value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                            class="w-full px-4 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-sm text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                        >
                    </div>

                    <!-- Is Active Checkbox -->
                    <div class="pt-4 sm:pt-6">
                        <label class="inline-flex items-center space-x-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1" 
                                checked 
                                class="w-5 h-5 rounded-lg bg-zinc-950 border-zinc-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-zinc-900"
                            >
                            <div>
                                <span class="text-sm font-semibold text-zinc-200">Aktifkan Langganan</span>
                                <p class="text-[11px] text-zinc-400">Sertakan dalam perhitungan metrik bulanan & tahunan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-6 border-t border-zinc-800/80 flex items-center justify-end space-x-3">
                    <button 
                        type="button" 
                        @click="modalOpen = false" 
                        class="px-5 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-semibold transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all cursor-pointer"
                    >
                        Simpan Langganan
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
