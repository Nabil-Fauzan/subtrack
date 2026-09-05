<!-- Modal: Edit Subscription -->
<div 
    x-show="editModalOpen" 
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
        @click.outside="editModalOpen = false"
        x-show="editModalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-xl rounded-3xl bg-white dark:bg-[#121318] border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 sm:p-8 text-zinc-900 dark:text-zinc-100 overflow-hidden"
    >
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-5 border-b border-zinc-200 dark:border-zinc-800/80 relative z-10">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">Edit Langganan</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Perbarui rincian data langganan yang dipilih</p>
                </div>
            </div>
            <button type="button" @click="editModalOpen = false" class="p-2 rounded-xl text-zinc-400 hover:text-zinc-600 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form :action="'{{ url('/subscriptions') }}/' + editData.id" method="POST" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Service Name -->
            <div>
                <label for="edit_service_name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Nama Layanan / Tool <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="edit_service_name" 
                    name="service_name" 
                    x-model="editData.service_name"
                    required 
                    placeholder="Contoh: GitHub Copilot, Spotify"
                    class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                >
            </div>

            <!-- Category & Payment Method Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label for="edit_category_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="edit_category_id" 
                        name="category_id" 
                        x-model="editData.category_id"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="" disabled>Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="edit_payment_method_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Metode Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="edit_payment_method_id" 
                        name="payment_method_id" 
                        x-model="editData.payment_method_id"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="" disabled>Pilih Metode Bayar</option>
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
                    <label for="edit_price" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Harga Asli <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        step="any"
                        min="0" 
                        id="edit_price" 
                        name="price" 
                        x-model="editData.price"
                        required 
                        placeholder="150000"
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                    >
                </div>

                <!-- Currency -->
                <div class="sm:col-span-1">
                    <label for="edit_currency" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Mata Uang <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="edit_currency" 
                        name="currency" 
                        x-model="editData.currency"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="IDR">IDR (Rupiah)</option>
                        <option value="USD">USD ($ - Kurs Live ~16.250)</option>
                        <option value="EUR">EUR (€ - Kurs Live ~17.600)</option>
                        <option value="SGD">SGD (S$ - Kurs Live ~12.550)</option>
                        <option value="GBP">GBP (£ - Kurs Live ~21.100)</option>
                    </select>
                </div>

                <!-- Billing Cycle -->
                <div class="sm:col-span-1">
                    <label for="edit_billing_cycle" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Siklus Penagihan <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="edit_billing_cycle" 
                        name="billing_cycle" 
                        x-model="editData.billing_cycle"
                        required
                        class="w-full px-4 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                    >
                        <option value="monthly">Monthly (Bulanan)</option>
                        <option value="quarterly">Quarterly (3 Bulan)</option>
                        <option value="yearly">Yearly (Tahunan)</option>
                    </select>
                </div>
            </div>

            <!-- Next Billing Date & Active Toggle -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <!-- Next Billing Date -->
                <div>
                    <label for="edit_next_billing_date" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Next Billing Date <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="edit_next_billing_date" 
                        name="next_billing_date" 
                        x-model="editData.next_billing_date"
                        required
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
                            x-model="editData.is_active"
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
                    @click="editModalOpen = false" 
                    class="px-5 py-2.5 rounded-xl bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-300 text-sm font-semibold transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-indigo-600 hover:from-amber-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-lg shadow-amber-600/30 transition-all cursor-pointer"
                >
                    Perbarui Langganan
                </button>
            </div>
        </form>
    </div>
</div>
