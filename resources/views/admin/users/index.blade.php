@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">Manage Users</h1>
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

    <!-- Search/Filter (placeholder for future implementation) -->
    <div class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2 max-w-md">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search users by name or username..." class="flex-1 px-3 py-1.5 text-sm border border-[#C8C0B0] rounded focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
            <button type="submit" class="px-4 py-1.5 bg-[#F4F1EA] text-[#333] text-sm font-semibold border border-[#C8C0B0] rounded hover:bg-[#EAE6DB] transition-colors">Search</button>
        </form>
    </div>

    <div class="bg-white rounded-md shadow-sm border border-[#DDD8CC] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#F4F1EA] border-b border-[#DDD8CC]">
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">User</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Email</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Joined</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Role</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Status</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#DDD8CC]">
                @forelse($users as $user)
                    <tr class="hover:bg-[#F4F1EA] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar_url)
                                    <img src="{{ Storage::url($user->avatar_url) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs">
                                        {{ substr($user->username, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-[#382110] text-sm">{{ $user->name }}</p>
                                    <p class="text-xs text-[#888]">{{ '@' . $user->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[#555] text-sm">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 text-[#555] text-sm">
                            {{ $user->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-[#382110] text-white rounded">Admin</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-gray-200 text-gray-700 rounded">User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_banned)
                                <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Banned</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('profile.show', $user->username) }}" target="_blank" class="inline-block px-3 py-1 text-xs font-semibold text-[#00635D] border border-[#00635D] rounded hover:bg-[#00635D] hover:text-white transition-colors mr-2">View</a>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-[#999] text-sm">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
