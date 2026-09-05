<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('subtrack_theme') !== 'light',
    modalOpen: false, 
    editModalOpen: false,
    importModalOpen: false,
    fileName: '',
    selectedIds: [],
    subscriptionsMap: {},
    initSubscriptions(subs) {
        let map = {};
        subs.forEach(s => {
            map[s.id] = s;
        });
        this.subscriptionsMap = map;
    },
    toggleSelectAll(ids) {
        if (this.selectedIds.length === ids.length) {
            this.selectedIds = [];
        } else {
            this.selectedIds = [...ids];
        }
    },
    get selectedMonthlySavings() {
        return this.selectedIds.reduce((sum, id) => {
            const item = this.subscriptionsMap[id];
            return sum + (item ? parseFloat(item.normalized_monthly_cost || 0) : 0);
        }, 0);
    },
    get selectedYearlySavings() {
        return this.selectedIds.reduce((sum, id) => {
            const item = this.subscriptionsMap[id];
            return sum + (item ? parseFloat(item.normalized_yearly_cost || 0) : 0);
        }, 0);
    },
    formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },
    editData: {
        id: '',
        service_name: '',
        category_id: '',
        payment_method_id: '',
        price: '',
        currency: 'IDR',
        billing_cycle: 'monthly',
        next_billing_date: '',
        is_active: true
    },
    openEditModal(sub) {
        this.editData = {
            id: sub.id,
            service_name: sub.service_name,
            category_id: sub.category_id,
            payment_method_id: sub.payment_method_id,
            price: sub.price,
            currency: sub.currency || 'IDR',
            billing_cycle: sub.billing_cycle,
            next_billing_date: sub.next_billing_date ? sub.next_billing_date.substring(0, 10) : '',
            is_active: !!sub.is_active
        };
        this.editModalOpen = true;
    },
    filterCategory: 'all', 
    filterCycle: 'all', 
    filterStatus: 'all',
    searchQuery: ''
}" 
x-init="
    $watch('darkMode', val => { 
        localStorage.setItem('subtrack_theme', val ? 'dark' : 'light'); 
        if (val) { 
            document.documentElement.classList.add('dark'); 
        } else { 
            document.documentElement.classList.remove('dark'); 
        } 
    });
    if (!darkMode) {
        document.documentElement.classList.remove('dark');
    }
"
:class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SubTrack - Platform analitik biaya langganan SaaS dan recurring cost analyzer dengan normalisasi multi-mata uang otomatis, kalkulator penghematan, dan pelacak pagu anggaran.">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SubTrack - Recurring Cost & Subscription Intelligence')</title>
    
    <!-- Custom Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Theme Detection (Anti-FOUC) -->
    <script>
        if (localStorage.getItem('subtrack_theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Laravel Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #3f3f46;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="min-h-screen bg-zinc-100 dark:bg-[#09090b] text-zinc-900 dark:text-zinc-100 antialiased selection:bg-indigo-500 selection:text-white">

    @yield('content')

</body>
</html>
