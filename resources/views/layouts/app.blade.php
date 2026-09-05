<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SubTrack - Recurring Cost & Subscription Analyzer')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Laravel Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #09090b;
            color: #f4f4f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #09090b;
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }
    </style>
</head>
<body class="min-h-screen bg-[#09090b] text-zinc-100 antialiased selection:bg-indigo-500 selection:text-white" x-data="{ 
    modalOpen: false, 
    editModalOpen: false,
    selectedIds: [],
    toggleSelectAll(ids) {
        if (this.selectedIds.length === ids.length) {
            this.selectedIds = [];
        } else {
            this.selectedIds = [...ids];
        }
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
}">

    @yield('content')

</body>
</html>
