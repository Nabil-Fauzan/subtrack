<!-- Subscription Management Section -->
<div class="rounded-3xl bg-zinc-900/60 border border-zinc-800/80 shadow-xl overflow-hidden backdrop-blur-md relative">
    
    <!-- Bulk Action Toolbar (Appears when items are selected) -->
    <div 
        x-show="selectedIds.length > 0"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="bg-gradient-to-r from-rose-950/90 via-zinc-900/95 to-indigo-950/90 border-b border-rose-500/30 px-6 py-3.5 flex flex-wrap items-center justify-between gap-4 sticky top-20 z-20 backdrop-blur-lg"
        style="display: none;"
    >
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-white tracking-tight">Tindakan Massal</span>
                <span class="text-xs text-rose-300 ml-1.5 font-medium">(<span x-text="selectedIds.length"></span> langganan dipilih)</span>
            </div>
        </div>

        <div class="flex items-center space-x-2.5">
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
                @submit="return confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' langganan yang dipilih secara permanen?');"
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
                    <th class="py-4 pl-6 pr-2 w-10 text-center">
                        <input 
                            type="checkbox" 
                            @change="toggleSelectAll({{ json_encode($subscriptions->pluck('id')) }})" 
                            :checked="selectedIds.length === {{ $subscriptions->count() }} && {{ $subscriptions->count() }} > 0"
                            title="Pilih Semua"
                            class="w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-zinc-900 cursor-pointer"
                        >
                    </th>
                    <th class="py-4 px-4">Layanan</th>
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
                        :class="selectedIds.includes({{ $sub->id }}) ? 'bg-indigo-500/5' : ''"
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
                                class="w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-zinc-900 cursor-pointer"
                            >
                        </td>

                        <!-- Service Name & Icon -->
                        <td class="py-4 px-4">
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

                        <!-- Actions (Edit & Delete) -->
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <!-- Edit Button -->
                                <button 
                                    type="button"
                                    @click="openEditModal({{ json_encode($sub) }})" 
                                    class="p-2 rounded-xl text-zinc-400 hover:text-amber-400 hover:bg-amber-500/10 transition-colors cursor-pointer"
                                    title="Edit Langganan"
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
                                        class="p-2 rounded-xl text-zinc-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                                        title="Hapus Langganan"
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
                        <td colspan="9" class="py-12 text-center text-zinc-500">
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
        <div class="flex items-center space-x-2">
            <span>Menampilkan {{ $subscriptions->count() }} total langganan</span>
            <span class="text-zinc-600">&bull;</span>
            <span class="text-indigo-400" x-show="selectedIds.length > 0">(<span x-text="selectedIds.length"></span> dipilih)</span>
        </div>
        <span class="text-zinc-500">SubTrack System v1.0 &bull; Auto Normalized Currency IDR</span>
    </div>
</div>
