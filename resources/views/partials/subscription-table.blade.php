<!-- Subscription Management Section -->
<div 
    x-init="initSubscriptions({{ json_encode($subscriptions) }})"
    class="rounded-3xl bg-white dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800/80 shadow-xl overflow-hidden backdrop-blur-md relative transition-colors duration-200"
>
    
    <!-- Bulk Action & 'What-If' Savings Simulator Toolbar -->
    <div 
        x-show="selectedIds.length > 0"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="bg-gradient-to-r from-rose-950/90 via-zinc-900/95 to-indigo-950/90 border-b border-rose-500/30 px-6 py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-20 z-20 backdrop-blur-lg"
        style="display: none;"
    >
        <!-- Left: Selection Info & What-If Savings Calculator -->
        <div class="flex items-center space-x-3.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-bold text-white tracking-tight"><span x-text="selectedIds.length"></span> Langganan Dipilih</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">What-If Simulator</span>
                </div>
                <p class="text-xs text-zinc-300 mt-0.5">
                    Potensi Penghematan: <span class="font-bold text-emerald-400" x-text="formatRupiah(selectedMonthlySavings) + '/bln'"></span> 
                    <span class="text-zinc-400 font-normal">(<span x-text="formatRupiah(selectedYearlySavings) + '/thn'"></span>)</span>
                </p>
            </div>
        </div>

        <!-- Right: Actions (Cancel Selection & Bulk Delete) -->
        <div class="flex items-center space-x-2.5 self-end md:self-auto">
            <button 
                type="button" 
                @click="selectedIds = []" 
                class="px-3.5 py-1.5 rounded-xl bg-zinc-800/80 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-colors cursor-pointer"
            >
                Batal Pilih
            </button>

            <form 
                action="{{ route('subscriptions.bulk-destroy') }}" 
                method="POST" 
                @submit="if (!confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' langganan yang dipilih secara permanen?')) { $event.preventDefault(); }"
            >
                @csrf
                @method('DELETE')
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button 
                    type="submit" 
                    class="inline-flex items-center space-x-1.5 px-4 py-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-semibold shadow-lg shadow-rose-600/30 transition-all cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus Terpilih (<span x-text="selectedIds.length"></span>)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Filter, Search, Import & Export Bar -->
    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">Daftar Langganan</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Kelola dan pantau seluruh siklus langganan Anda secara realtime</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Search Input -->
            <div class="relative min-w-[160px] flex-1 sm:flex-initial">
                <input 
                    type="text" 
                    name="search"
                    x-model="searchQuery" 
                    aria-label="Cari nama layanan"
                    placeholder="Cari layanan..." 
                    class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-950/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-200 placeholder-zinc-500 dark:placeholder-zinc-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                >
                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Category Filter -->
            <select 
                x-model="filterCategory" 
                aria-label="Filter berdasarkan kategori"
                class="px-3 py-2 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-950/80 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
            >
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Cycle Filter -->
            <select 
                x-model="filterCycle" 
                aria-label="Filter berdasarkan siklus penagihan"
                class="px-3 py-2 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-950/80 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
            >
                <option value="all">Semua Siklus</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>

            <!-- Status Filter -->
            <select 
                x-model="filterStatus" 
                aria-label="Filter berdasarkan status langganan"
                class="px-3 py-2 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-950/80 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
            >
                <option value="all">Semua Status</option>
                <option value="active">Hanya Aktif</option>
                <option value="inactive">Hanya Nonaktif</option>
                <option value="renewing_soon">Segera Perpanjang (&le;7hr)</option>
            </select>

            <!-- Import CSV Button -->
            <button 
                type="button"
                @click="importModalOpen = true"
                class="inline-flex items-center space-x-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800/80 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700/60 shadow-sm transition-all duration-200 cursor-pointer active:scale-95"
                title="Impor langganan dari berkas CSV"
            >
                <svg class="w-3.5 h-3.5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Import CSV</span>
            </button>

            <!-- Export CSV Button -->
            <a 
                href="{{ route('subscriptions.export') }}" 
                class="inline-flex items-center space-x-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800/80 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700/60 shadow-sm transition-all duration-200 cursor-pointer active:scale-95"
                title="Unduh seluruh data langganan dalam format CSV"
            >
                <svg class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- Table View -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-800/80 bg-zinc-50/70 dark:bg-zinc-950/40 text-[11px] uppercase tracking-wider font-semibold text-zinc-600 dark:text-zinc-400">
                    <th class="py-4 pl-6 pr-2 w-10 text-center">
                        <input 
                            type="checkbox" 
                            @change="toggleSelectAll({{ json_encode($subscriptions->pluck('id')) }})" 
                            :checked="selectedIds.length === {{ $subscriptions->count() }} && {{ $subscriptions->count() }} > 0"
                            title="Pilih Semua"
                            aria-label="Pilih semua langganan"
                            class="w-4 h-4 rounded bg-white dark:bg-zinc-950 border-zinc-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                        >
                    </th>
                    <th class="py-4 px-4">Layanan</th>
                    <th class="py-4 px-4">Kategori</th>
                    <th class="py-4 px-4">Biaya Asli</th>
                    <th class="py-4 px-4">Ekuivalen IDR</th>
                    <th class="py-4 px-4">Next Billing</th>
                    <th class="py-4 px-4">Metode Bayar</th>
                    <th class="py-4 px-4 text-center">Status</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800/60 text-sm">
                @forelse($subscriptions as $sub)
                    @php
                        $daysDiff = $sub->days_until_renewal;
                        $isForeignCurrency = ($sub->currency && $sub->currency !== 'IDR');
                    @endphp
                    <tr 
                        class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors {{ !$sub->is_active ? 'opacity-50 hover:opacity-80' : '' }}"
                        :class="selectedIds.includes({{ $sub->id }}) ? 'bg-indigo-500/10 dark:bg-indigo-500/5' : ''"
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
                        <!-- Selection Checkbox -->
                        <td class="py-4 pl-6 pr-2 text-center">
                            <input 
                                type="checkbox" 
                                :value="{{ $sub->id }}" 
                                x-model.number="selectedIds"
                                aria-label="Pilih langganan {{ $sub->service_name }}"
                                class="w-4 h-4 rounded bg-white dark:bg-zinc-950 border-zinc-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                            >
                        </td>

                        <!-- Service Name & Icon -->
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3.5">
                                <div 
                                    class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm shadow-sm" 
                                    style="background: {{ $sub->category->color_hex }}1a; color: {{ $sub->category->color_hex }}; border: 1px solid {{ $sub->category->color_hex }}40;"
                                >
                                    {{ strtoupper(substr($sub->service_name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                                        <span>{{ $sub->service_name }}</span>
                                    </div>
                                    <span class="text-xs text-zinc-600 dark:text-zinc-300 font-medium">ID #{{ $sub->id }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Category Badge -->
                        <td class="py-4 px-4">
                            <span 
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-zinc-900 dark:text-zinc-100"
                                style="background-color: {{ $sub->category->color_hex }}22; border: 1px solid {{ $sub->category->color_hex }}66;"
                            >
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 shadow-sm" style="background-color: {{ $sub->category->color_hex }};"></span>
                                {{ $sub->category->name }}
                            </span>
                        </td>

                        <!-- Original Price & Currency Badge -->
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-1.5">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $sub->formatted_original_price }}
                                </span>
                                @if($isForeignCurrency)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30">
                                        {{ $sub->currency }}
                                    </span>
                                @endif
                            </div>
                            <span class="inline-block text-[11px] font-medium text-zinc-600 dark:text-zinc-300 capitalize">
                                / {{ $sub->billing_cycle }}
                            </span>
                        </td>

                        <!-- Normalized Monthly Cost (IDR) -->
                        <td class="py-4 px-4">
                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                Rp {{ number_format($sub->normalized_monthly_cost, 0, ',', '.') }}
                            </div>
                            <span class="text-[10px] text-zinc-600 dark:text-zinc-300 font-medium">per bulan</span>
                        </td>

                        <!-- Next Billing Date & Renewal Status -->
                        <td class="py-4 px-4">
                            <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                                {{ \Carbon\Carbon::parse($sub->next_billing_date)->format('d M Y') }}
                            </div>
                            <div class="mt-1">
                                @if($sub->is_overdue)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30">
                                        ⚠️ Terlewat (Overdue)
                                    </span>
                                @elseif($sub->is_renewing_soon)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/15 text-amber-800 dark:text-amber-200 border border-amber-500/30 animate-pulse">
                                        ⏳ Perpanjang {{ $daysDiff == 0 ? 'Hari ini' : 'dalam ' . $daysDiff . ' hari' }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-zinc-600 dark:text-zinc-300 font-medium">
                                        {{ $daysDiff }} hari lagi
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Payment Method -->
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-zinc-100 dark:bg-zinc-800/80 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700/60">
                                <svg class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer {{ $sub->is_active ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-700' }}"
                                >
                                    <span class="w-2 h-2 rounded-full {{ $sub->is_active ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-zinc-400 dark:bg-zinc-500' }}"></span>
                                    <span>{{ $sub->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </form>
                        </td>

                        <!-- Actions (Edit & Delete) -->
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <!-- Edit Button -->
                                <button 
                                    type="button"
                                    @click="openEditModal({{ json_encode($sub) }})" 
                                    class="p-2 rounded-xl text-zinc-500 dark:text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-500/10 transition-colors cursor-pointer"
                                    title="Edit Langganan"
                                    aria-label="Edit langganan {{ $sub->service_name }}"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <!-- Delete Form -->
                                <form action="{{ route('subscriptions.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus langganan {{ $sub->service_name }}?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="p-2 rounded-xl text-zinc-500 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                                        title="Hapus Langganan"
                                        aria-label="Hapus langganan {{ $sub->service_name }}"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-14 text-center text-zinc-500">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-400 dark:text-zinc-600 mb-3 shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <p class="text-base font-semibold text-zinc-700 dark:text-zinc-300">Belum ada data langganan</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm mx-auto">Tambahkan langganan pertama Anda, impor dari CSV, atau gunakan data demo siap pakai.</p>
                            
                            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                                <form action="{{ route('demo.seed') }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 text-xs font-bold rounded-xl bg-gradient-to-r from-amber-600 to-indigo-600 hover:from-amber-500 hover:to-indigo-500 text-white shadow-lg shadow-indigo-600/25 transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span>✨ Muat Data Demo</span>
                                    </button>
                                </form>
                                <button @click="importModalOpen = true" class="px-4 py-2 text-xs font-semibold rounded-xl bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-300 transition-colors cursor-pointer">
                                    Import CSV
                                </button>
                                <button @click="modalOpen = true" class="px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-colors cursor-pointer">
                                    + Tambah Manual
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer Stats -->
    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-950/40 flex flex-col sm:flex-row items-center justify-between text-xs text-zinc-600 dark:text-zinc-300 gap-2">
        <div class="flex items-center space-x-2">
            <span>Menampilkan {{ $subscriptions->count() }} total langganan</span>
            <span class="text-zinc-400 dark:text-zinc-600">&bull;</span>
            <span class="text-indigo-600 dark:text-indigo-400 font-semibold" x-show="selectedIds.length > 0">(<span x-text="selectedIds.length"></span> dipilih)</span>
        </div>
        <span class="text-zinc-600 dark:text-zinc-300 font-medium">Multi-Currency &bull; Kurs Otomatis IDR</span>
    </div>
</div>
