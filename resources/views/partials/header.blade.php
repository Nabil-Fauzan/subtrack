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
