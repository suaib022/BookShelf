@extends('layouts.admin')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
            </div>
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        
        <!-- Total Books Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Books</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_books']) }}</p>
            </div>
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>
        
        <!-- Total Reviews Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Reviews</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_reviews']) }}</p>
            </div>
            <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
        </div>
        
    </div>
</div>
@endsection
