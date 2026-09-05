<!-- Modal: Import CSV -->
<div 
    x-show="importModalOpen" 
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
        @click.outside="importModalOpen = false"
        x-show="importModalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-[#121318] border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 sm:p-8 text-zinc-900 dark:text-zinc-100 overflow-hidden"
    >
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-5 border-b border-zinc-200 dark:border-zinc-800/80 relative z-10">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">Impor Berkas CSV</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Unggah berkas spreadsheet langganan Anda</p>
                </div>
            </div>
            <button type="button" @click="importModalOpen = false" class="p-2 rounded-xl text-zinc-400 hover:text-zinc-600 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('subscriptions.import') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf

            <!-- Upload Box -->
            <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-emerald-500/60 rounded-2xl p-6 text-center transition-colors">
                <svg class="w-10 h-10 mx-auto text-zinc-400 dark:text-zinc-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <label for="csv_file" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer">
                    Pilih Berkas CSV / TXT
                    <input 
                        type="file" 
                        id="csv_file" 
                        name="csv_file" 
                        accept=".csv,.txt"
                        required
                        class="hidden"
                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                    >
                </label>
                <p class="text-xs text-zinc-500 mt-1" x-text="fileName ? 'Terpilih: ' + fileName : 'Format kolom: Nama Layanan, Kategori, Metode Bayar, Harga, Mata Uang, Siklus, Jatuh Tempo'"></p>
            </div>

            <!-- Hint box -->
            <div class="p-3.5 rounded-xl bg-zinc-100 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 text-[11px] text-zinc-600 dark:text-zinc-400 space-y-1">
                <p class="font-bold text-zinc-800 dark:text-zinc-200">💡 Format Kolom CSV yang Didukung:</p>
                <p class="font-mono text-[10px] text-indigo-600 dark:text-indigo-400">Nama Layanan, Kategori, Metode Bayar, Harga Asli, Mata Uang, Siklus Penagihan, Jatuh Tempo</p>
                <p class="text-[10px] text-zinc-500">*Proses impor atomik & aman dilindungi database transactions.</p>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800/80 flex items-center justify-end space-x-3">
                <button 
                    type="button" 
                    @click="importModalOpen = false" 
                    class="px-5 py-2.5 rounded-xl bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-300 text-sm font-semibold transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-semibold shadow-lg shadow-emerald-600/30 transition-all cursor-pointer"
                >
                    Mulai Impor CSV
                </button>
            </div>
        </form>
    </div>
</div>
