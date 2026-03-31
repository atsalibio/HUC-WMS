@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4">
    <h1 class="text-3xl font-bold">Warehouse Dashboard</h1>
    <p class="text-sm text-slate-500 mt-2">Warehouse-specific view.</p>
    <div class="mt-6">
        @include('dashboard')
    </div>
</div>
@endsection