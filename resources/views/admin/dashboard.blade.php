@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">Admin Dashboard</h1>
    </div>

    <!-- Admin Navigation Tabs -->
    <div class="flex border-b border-[#DDD8CC] mb-8">
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-[#00635D] text-[#00635D] font-bold' : 'border-transparent text-[#555] hover:text-[#333] hover:border-[#C8C0B0]' }} text-sm transition-colors">
            Overview
        </a>
        <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border-b-2 {{ request()->routeIs('admin.books.*') ? 'border-[#00635D] text-[#00635D] font-bold' : 'border-transparent text-[#555] hover:text-[#333] hover:border-[#C8C0B0]' }} text-sm transition-colors">
            Books
        </a>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-[#00635D] text-[#00635D] font-bold' : 'border-transparent text-[#555] hover:text-[#333] hover:border-[#C8C0B0]' }} text-sm transition-colors">
            Users
        </a>
    </div>

    <!-- Dashboard Content -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        
        <!-- Total Users Card -->
        <div class="bg-white rounded-md shadow-sm border border-[#DDD8CC] p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#555] uppercase tracking-widest mb-1">Total Users</p>
                <p class="text-3xl font-bold text-[#382110]">{{ number_format($stats['total_users'] ?? 0) }}</p>
            </div>
            <div class="text-[#00635D] p-3 rounded-full bg-[#F4F1EA]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        
        <!-- Total Books Card -->
        <div class="bg-white rounded-md shadow-sm border border-[#DDD8CC] p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#555] uppercase tracking-widest mb-1">Total Books</p>
                <p class="text-3xl font-bold text-[#382110]">{{ number_format($stats['total_books'] ?? 0) }}</p>
            </div>
            <div class="text-[#5C7A3E] p-3 rounded-full bg-[#F4F1EA]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>
        
        <!-- Total Reviews Card -->
        <div class="bg-white rounded-md shadow-sm border border-[#DDD8CC] p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#555] uppercase tracking-widest mb-1">Total Reviews</p>
                <p class="text-3xl font-bold text-[#382110]">{{ number_format($stats['total_reviews'] ?? 0) }}</p>
            </div>
            <div class="text-[#888] p-3 rounded-full bg-[#F4F1EA]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
        </div>
        
    </div>
</div>
@endsection
