<!-- Top Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    
    <!-- Monthly Spend with Budget Cap & Health Indicator -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800/80 p-6 shadow-xl hover:border-zinc-300 dark:hover:border-zinc-700/60 transition-all duration-300 group">
        <div class="absolute -right-6 -top-6 w-28 h-28 {{ $metrics['is_overbudget'] ? 'bg-rose-500/15' : 'bg-indigo-500/10' }} rounded-full blur-2xl group-hover:opacity-100 transition-all pointer-events-none"></div>
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Monthly Cost</span>
            <div class="w-10 h-10 rounded-xl {{ $metrics['is_overbudget'] ? 'bg-rose-500/10 border-rose-500/20 text-rose-500 dark:text-rose-400' : 'bg-indigo-500/10 border-indigo-500/20 text-indigo-600 dark:text-indigo-400' }} border flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="space-y-1">
            <div class="text-2xl lg:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                Rp {{ number_format($metrics['total_monthly_cost'], 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between">
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Pagu: Rp {{ number_format($metrics['budget_cap'], 0, ',', '.') }}/bln</p>
                @if($metrics['is_overbudget'])
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/15 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-500/30">
                        Overbudget +{{ round($metrics['budget_percentage'] - 100, 1) }}%
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/15 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                        Terkendali ({{ $metrics['budget_percentage'] }}%)
                    </span>
                @endif
            </div>
        </div>

        <!-- Budget Cap Progress Bar -->
        <div class="mt-3.5">
            <div class="w-full bg-zinc-100 dark:bg-zinc-800/80 rounded-full h-1.5 overflow-hidden flex">
                <div 
                    class="h-full rounded-full transition-all duration-500 {{ $metrics['is_overbudget'] ? 'bg-rose-500' : 'bg-gradient-to-r from-indigo-500 to-emerald-400' }}" 
                    style="width: {{ min(100, $metrics['budget_percentage']) }}%"
                ></div>
            </div>
        </div>

        <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span>Estimasi Harian</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-300">Rp {{ number_format($metrics['total_monthly_cost'] / 30, 0, ',', '.') }}/hari</span>
        </div>
    </div>

    <!-- Projected Yearly Spend -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800/80 p-6 shadow-xl hover:border-zinc-300 dark:hover:border-zinc-700/60 transition-all duration-300 group">
        <div class="absolute -right-6 -top-6 w-28 h-28 bg-violet-500/10 rounded-full blur-2xl group-hover:bg-violet-500/20 transition-all pointer-events-none"></div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Projected Yearly Cost</span>
            <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-600 dark:text-violet-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>
        <div class="space-y-1">
            <div class="text-2xl lg:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                Rp {{ number_format($metrics['total_yearly_cost'], 0, ',', '.') }}
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Proyeksi beban biaya per 12 bulan</p>
        </div>
        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span>Rata-rata per layanan</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-300">
                @if($metrics['active_count'] > 0)
                    Rp {{ number_format($metrics['total_monthly_cost'] / $metrics['active_count'], 0, ',', '.') }}/bln
                @else
                    Rp 0
                @endif
            </span>
        </div>
    </div>

    <!-- Active vs Inactive Subscriptions -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800/80 p-6 shadow-xl hover:border-zinc-300 dark:hover:border-zinc-700/60 transition-all duration-300 group">
        <div class="absolute -right-6 -top-6 w-28 h-28 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all pointer-events-none"></div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Subscription Status</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="space-y-1">
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl lg:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $metrics['active_count'] }}</span>
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Aktif</span>
                <span class="text-zinc-400 dark:text-zinc-600">/</span>
                <span class="text-lg font-bold text-zinc-600 dark:text-zinc-300">{{ $metrics['inactive_count'] }}</span>
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Nonaktif</span>
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-400">Total {{ $subscriptions->count() }} layanan terdaftar</p>
        </div>
        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/60">
            <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden flex">
                @php
                    $totalSubs = $subscriptions->count();
                    $activePercent = $totalSubs > 0 ? ($metrics['active_count'] / $totalSubs) * 100 : 0;
                @endphp
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $activePercent }}%"></div>
            </div>
        </div>
    </div>

    <!-- Renewals & Overdue Alert -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800/80 p-6 shadow-xl hover:border-zinc-300 dark:hover:border-zinc-700/60 transition-all duration-300 group">
        <div class="absolute -right-6 -top-6 w-28 h-28 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all pointer-events-none"></div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Billing Alerts</span>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="space-y-1">
            <div class="flex items-center space-x-3">
                <div class="flex items-baseline space-x-1.5">
                    <span class="text-2xl lg:text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $metrics['renewing_soon_count'] }}</span>
                    <span class="text-xs font-medium text-amber-600/80 dark:text-amber-300/80">Segera (&le;7hr)</span>
                </div>
                @if($metrics['overdue_count'] > 0)
                    <div class="flex items-baseline space-x-1 px-2 py-0.5 rounded-lg bg-rose-500/15 dark:bg-rose-500/20 border border-rose-500/30 text-rose-600 dark:text-rose-400">
                        <span class="text-xs font-bold">{{ $metrics['overdue_count'] }}</span>
                        <span class="text-[10px] uppercase">Lewat</span>
                    </div>
                @endif
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Perhatian perpanjangan langganan</p>
        </div>
        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between text-xs">
            <span class="text-zinc-500 dark:text-zinc-400">Status Perpanjangan</span>
            @if($metrics['renewing_soon_count'] > 0 || $metrics['overdue_count'] > 0)
                <span class="font-medium text-amber-600 dark:text-amber-400 flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Perlu Tindakan</span>
                </span>
            @else
                <span class="font-medium text-emerald-600 dark:text-emerald-400">Aman & Terkendali</span>
            @endif
        </div>
    </div>

</div>
