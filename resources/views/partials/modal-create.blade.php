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
        class="relative w-full max-w-xl rounded-3xl bg-white dark:bg-[#121318] border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 sm:p-8 text-zinc-900 dark:text-zinc-100 overflow-hidden"
    >
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-5 border-b border-zinc-200 dark:border-zinc-800/80 relative z-10">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">Tambah Langganan Baru</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Masukkan detail recurring cost untuk dianalisis</p>
                </div>
            </div>
            <button type="button" @click="modalOpen = false" class="p-2 rounded-xl text-zinc-400 hover:text-zinc-600 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-colors cursor-pointer">
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
                <label for="service_name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Nama Layanan / Tool <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="service_name" 
                    name="service_name" 
                    required 
                    placeholder="Contoh: GitHub Copilot, Spotify, ChatGPT Plus, AWS"
                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                >
            </div>

            <!-- Category & Payment Method Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="category_id" 
                        name="category_id" 
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Metode Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="payment_method_id" 
                        name="payment_method_id" 
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="" disabled selected>Pilih Metode Bayar</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Price, Currency & Billing Cycle Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Price -->
                <div class="sm:col-span-1">
                    <label for="price" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Harga Asli <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        step="any"
                        min="0" 
                        id="price" 
                        name="price" 
                        required 
                        placeholder="150000"
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                    >
                </div>

                <!-- Currency -->
                <div class="sm:col-span-1">
                    <label for="currency" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Mata Uang <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="currency" 
                        name="currency" 
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="IDR" selected>IDR (Rupiah)</option>
                        <option value="USD">USD ($ - Kurs Live ~16.250)</option>
                        <option value="EUR">EUR (€ - Kurs Live ~17.600)</option>
                        <option value="SGD">SGD (S$ - Kurs Live ~12.550)</option>
                        <option value="GBP">GBP (£ - Kurs Live ~21.100)</option>
                    </select>
                </div>

                <!-- Billing Cycle -->
                <div class="sm:col-span-1">
                    <label for="billing_cycle" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Siklus Penagihan <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="billing_cycle" 
                        name="billing_cycle" 
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="monthly" selected>Monthly (Bulanan)</option>
                        <option value="quarterly">Quarterly (3 Bulan)</option>
                        <option value="yearly">Yearly (Tahunan)</option>
                    </select>
                </div>
            </div>

            <!-- Next Billing Date & Active Toggle -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <!-- Next Billing Date -->
                <div>
                    <label for="next_billing_date" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Next Billing Date <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="next_billing_date" 
                        name="next_billing_date" 
                        required
                        value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
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
                            class="w-5 h-5 rounded-lg bg-zinc-100 dark:bg-zinc-950 border-zinc-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500"
                        >
                        <div>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-200">Aktifkan Langganan</span>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Sertakan dalam normalisasi metrik IDR</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800/80 flex items-center justify-end space-x-3">
                <button 
                    type="button" 
                    @click="modalOpen = false" 
                    class="px-5 py-2.5 rounded-xl bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-300 text-sm font-semibold transition-colors cursor-pointer"
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
