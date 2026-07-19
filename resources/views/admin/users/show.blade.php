@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-[#888] hover:text-[#333] transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">User Details</h1>
    </div>

    <div class="flex flex-col md:flex-row gap-8 items-start">
        <!-- Left column -->
        <aside class="w-full md:w-[300px] shrink-0 bg-white rounded-md shadow-sm border border-[#DDD8CC] p-6">
            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-[#DDD8CC] mb-4">
                    @if($user->avatar_url)
                        <img src="{{ filter_var($user->avatar_url, FILTER_VALIDATE_URL) ? $user->avatar_url : Storage::url($user->avatar_url) }}" alt="{{ $user->username }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-2xl">
                            {{ substr($user->username, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-[#382110]">{{ $user->name }}</h2>
                <p class="text-sm text-[#888]">@{{ $user->username }}</p>
            </div>

            <div class="space-y-4 text-sm text-[#555]">
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-[#888] mb-1">Email</span>
                    {{ $user->email }}
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-[#888] mb-1">Joined</span>
                    {{ $user->created_at->format('M j, Y') }}
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-[#888] mb-1">Role</span>
                    @if($user->role === 'admin')
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-[#382110] text-white rounded">Admin</span>
                    @else
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-gray-200 text-gray-700 rounded">User</span>
                    @endif
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-[#888] mb-1">Status</span>
                    @if($user->is_banned)
                        <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Banned</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Active</span>
                    @endif
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-[#DDD8CC]">
                @if(auth()->id() !== $user->id)
                    @if($user->is_banned)
                        <form action="{{ route('admin.users.unban', $user) }}" method="POST" onsubmit="return confirm('Unban this user?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full px-4 py-2 bg-[#5C7A3E] text-white font-semibold rounded hover:bg-[#4a6332] transition-colors">Unban User</button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.ban', $user) }}" method="POST" onsubmit="return confirm('Ban this user?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full px-4 py-2 border border-red-600 text-red-600 font-semibold rounded hover:bg-red-50 transition-colors">Ban User</button>
                        </form>
                    @endif
                @endif
            </div>
        </aside>

        <!-- Right column: Activity Log -->
        <div class="flex-1 min-w-0 w-full bg-white rounded-md shadow-sm border border-[#DDD8CC] p-6">
            <h3 class="text-lg font-bold text-[#382110] mb-4 border-b border-[#DDD8CC] pb-2">Recent Activity</h3>
            
            @if($activities->count() > 0)
                <div class="space-y-6">
                    @foreach($activities as $activity)
                        <div class="flex gap-4">
                            <div class="w-8 flex flex-col items-center shrink-0">
                                <div class="w-8 h-8 rounded-full bg-[#F4F1EA] flex items-center justify-center text-[#555] mb-2">
                                    @if($activity['type'] === 'review')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    @endif
                                </div>
                                <div class="w-px h-full bg-[#DDD8CC]"></div>
                            </div>
                            <div class="flex-1 pb-6">
                                <a href="{{ $activity['url'] }}" class="text-sm font-bold text-[#00635D] hover:underline mb-1 block">{{ $activity['title'] }}</a>
                                <p class="text-sm text-[#555] mb-2 line-clamp-2">{{ $activity['content'] }}</p>
                                <p class="text-xs text-[#888]">{{ $activity['date']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-[#888] text-sm">No recent activity found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
