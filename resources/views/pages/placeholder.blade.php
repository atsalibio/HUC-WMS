@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16">
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $page ?? 'Maintenance')) }}</h2>
        <p class="mt-2 text-slate-500 dark:text-slate-300">This page is coming soon (migrated from legacy dashboard sidebar).</p>
        <a href="{{ route('dashboard') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">Go to Dashboard</a>
    </div>
</div>
@endsection