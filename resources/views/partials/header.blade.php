<!-- Top Navigation / Header -->
<header class="border-b border-zinc-200 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-950/70 backdrop-blur-xl sticky top-0 z-30 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 via-violet-600 to-emerald-400 p-[1px] shadow-lg shadow-indigo-500/20">
                <div class="w-full h-full bg-white dark:bg-[#09090b] rounded-2xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            <div>
                <div class="flex items-center space-x-2.5">
                    <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">SubTrack</h1>
                    <span class="px-2 py-0.5 text-xs font-semibold uppercase tracking-wider rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">Cost Analyzer</span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 hidden sm:block">Recurring Cost & Subscription Intelligence Dashboard</p>
            </div>
        </div>

        <div class="flex items-center space-x-2.5 sm:space-x-3">
            <!-- Theme Toggle Button (Dark / Light) -->
            <button 
                type="button" 
                @click="darkMode = !darkMode" 
                class="p-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800/80 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700/60 transition-all duration-200 cursor-pointer active:scale-95"
                :title="darkMode ? 'Beralih ke Light Mode' : 'Beralih ke Dark Mode'"
            >
                <!-- Sun icon (shown in dark mode) -->
                <svg x-show="darkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <!-- Moon icon (shown in light mode) -->
                <svg x-show="!darkMode" class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <!-- Quick Demo Data Button -->
            <form action="{{ route('demo.seed') }}" method="POST" class="inline-block" onsubmit="return confirm('Muat data demo realistis ke database?');">
                @csrf
                <button type="submit" class="inline-flex items-center space-x-2 px-3.5 py-2.5 text-sm font-semibold rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800/80 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 dark:hover:text-white border border-zinc-200 dark:border-zinc-700/60 transition-all duration-200 cursor-pointer active:scale-95" title="Generate Sample Realistic Subscriptions">
                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="hidden sm:inline">✨ Muat Data Demo</span>
                    <span class="sm:hidden">Demo</span>
                </button>
            </form>

            <!-- Add Subscription Button -->
            <button @click="modalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 transition-all duration-200 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Langganan</span>
            </button>
        </div>
    </div>
</header>
