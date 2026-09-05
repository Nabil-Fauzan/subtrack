@extends('layouts.app')

@section('title', 'SubTrack - Recurring Cost & Subscription Analyzer')

@section('content')
    <!-- Header Navigation -->
    @include('partials.header')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-between text-emerald-800 dark:text-emerald-300 shadow-lg shadow-emerald-950/5 dark:shadow-emerald-950/40">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 shadow-lg shadow-rose-950/5 dark:shadow-rose-950/40">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-rose-900 dark:text-rose-200">Terjadi kesalahan validasi:</h4>
                        <ul class="list-disc list-inside text-xs mt-1 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Metric Cards -->
        @include('partials.metrics')

        <!-- Category Breakdown -->
        @include('partials.category-breakdown')

        <!-- Payment Method Spending Breakdown & Cashflow Insights -->
        @include('partials.payment-breakdown')

        <!-- Subscription Table & Filters -->
        @include('partials.subscription-table')
    </main>

    <!-- Add Subscription Modal -->
    @include('partials.modal-create')

    <!-- Edit Subscription Modal -->
    @include('partials.modal-edit')

    <!-- Import CSV Modal -->
    @include('partials.modal-import')
@endsection
