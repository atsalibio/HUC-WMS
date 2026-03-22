@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 p-4">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8 w-full max-w-2xl text-center">
        <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">Welcome, {{  $user['FName'] ?? 'User' }}!</h1>
        <p class="text-gray-700 dark:text-gray-300 mb-6">Role: {{ $user['Role'] ?? 'N/A' }}</p>

        <div class="flex justify-center gap-4">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="btn btn-primary px-4 py-2 rounded bg-teal-600 text-white hover:bg-teal-700">
                Logout
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection