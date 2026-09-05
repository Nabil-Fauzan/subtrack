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
