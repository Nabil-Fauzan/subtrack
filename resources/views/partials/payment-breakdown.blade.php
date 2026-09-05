<!-- Payment Method Spending Breakdown & Cashflow Insights -->
<div class="rounded-3xl bg-white dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800/80 p-6 sm:p-7 shadow-xl backdrop-blur-md transition-colors duration-200">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 gap-4">
        <div>
            <h2 class="text-base font-bold text-zinc-900 dark:text-white tracking-tight flex items-center space-x-2">
                <span>Distribusi Beban per Metode Pembayaran</span>
            </h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Analisis arus kas (*cashflow insights*) berdasarkan sumber pembayaran aktif</p>
        </div>
        <div class="text-right sm:text-left">
            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Metode Aktif:</span>
            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400 ml-1">{{ $paymentBreakdown->count() }} Sumber</span>
        </div>
    </div>

    <!-- Grid of Payment Methods -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($paymentBreakdown as $pm)
            <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200 dark:border-zinc-800/60 relative overflow-hidden transition-all hover:border-zinc-300 dark:hover:border-zinc-700/60">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $pm['name'] }}</h3>
                            <span class="text-[11px] text-zinc-600 dark:text-zinc-400">{{ $pm['active_count'] }} langganan aktif</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 text-xs font-extrabold rounded-lg {{ $pm['is_heavy_burden'] ? 'bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/30' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}">
                        {{ $pm['percentage'] }}%
                    </span>
                </div>

                <div class="mt-3">
                    <div class="flex items-baseline justify-between text-xs mb-1.5">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Pengeluaran</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($pm['monthly_cost'], 0, ',', '.') }}/bln</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden flex">
                        <div 
                            class="h-full rounded-full transition-all duration-500 {{ $pm['is_heavy_burden'] ? 'bg-amber-500' : 'bg-indigo-500' }}" 
                            style="width: {{ $pm['percentage'] }}%"
                        ></div>
                    </div>
                </div>

                @if($pm['is_heavy_burden'])
                    <div class="mt-2.5 pt-2 border-t border-amber-500/20 flex items-center space-x-1.5 text-[10px] text-amber-700 dark:text-amber-400 font-medium">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Beban dominan (&gt;40% total pengeluaran). Pantau limit transaksi.</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-4 text-center text-xs text-zinc-500">
                Belum ada data pengeluaran metode pembayaran aktif.
            </div>
        @endforelse
    </div>
</div>
