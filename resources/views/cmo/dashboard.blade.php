@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4">
    <h1 class="text-3xl font-bold">CMO/GSO/COA Dashboard</h1>
    <p class="text-sm text-slate-500 mt-2">Oversee high-level procurement and inventory compliance.</p>
    <div class="mt-6">
        @include('dashboard')
    </div>
</div>
@endsection
