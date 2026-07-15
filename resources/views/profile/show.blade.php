@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    <div class="flex flex-col md:flex-row gap-8 items-start">
        
        <!-- Left column -->
        <aside class="w-full md:w-[260px] shrink-0">
            <div class="flex flex-col items-center text-center">
                <div class="w-[160px] h-[160px] rounded-full overflow-hidden border-4 border-white shadow-md mb-4">
                    @if($user->avatar_url)
                        <img src="{{ Storage::url($user->avatar_url) }}" alt="{{ $user->username }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-4xl">
                            {{ substr($user->username, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="space-y-1.5">
                    <div class="text-sm">
                        <a href="{{ route('profile.followers', $user->username) }}" class="text-[#00635D] hover:underline">
                            {{ $stats['followers'] }} followers
                        </a>
                        <span class="text-[#888]">&middot;</span>
                        <a href="{{ route('profile.following', $user->username) }}" class="text-[#00635D] hover:underline">
                            {{ $stats['following'] }} following
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right column -->
        <div class="flex-1 min-w-0 w-full">
            <!-- Profile Header -->
            <div class="mb-5">
                <div class="flex items-baseline gap-3 flex-wrap mb-3">
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-[#382110] leading-tight">{{ $user->name }}</h1>
                        @if($user->role === 'admin')
                            <div class="flex items-center gap-1">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-[#382110] text-white rounded">
                                    {{ strtoupper($user->active_mode ?? 'user') }}
                                </span>
                                @if($isOwnProfile)
                                    <form method="POST" action="{{ route('toggle-mode') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 rounded text-[#888] hover:text-[#00635D] hover:bg-[#F4F1EA] transition-colors" title="Switch to {{ $user->active_mode === 'admin' ? 'User' : 'Admin' }} Mode">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                    @if($isOwnProfile)
                        <div class="flex items-center gap-2">
                            <a href="{{ route('profile.edit') }}" class="text-sm text-[#00635D] hover:underline font-medium transition-colors">
                                (edit profile)
                            </a>
                            @if(auth()->user()->isActiveAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="text-sm text-[#00635D] hover:underline font-medium transition-colors">
                                    (admin dashboard)
                                </a>
                            @endif
                        </div>
                    @elseif(auth()->check())
                        <button id="follow-btn" 
                                data-user-id="{{ $user->id }}" 
                                data-is-following="{{ $isFollowing ? 'true' : 'false' }}"
                                class="px-4 py-1.5 rounded text-sm font-semibold transition-colors {{ $isFollowing ? 'border border-[#C8C0B0] text-[#555] hover:border-[#999] hover:text-[#333]' : 'bg-[#5C7A3E] hover:opacity-90 text-white' }}">
                            {{ $isFollowing ? 'Following' : 'Follow' }}
                        </button>
                    @endif
                </div>
                <div class="space-y-1">
                    @if($user->location)
                    <div class="flex items-baseline gap-4">
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#888] w-20">Location</span>
                        <span class="text-sm text-[#444]">{{ $user->location }}</span>
                    </div>
                    @endif
                    <div class="flex items-baseline gap-4">
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#888] w-20">Joined</span>
                        <span class="text-sm text-[#444]">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Shelves Row -->
            <div class="bg-white border border-[#DDD8CC] rounded-md p-4 mb-5">
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3 flex items-center justify-between">
                    Shelves
                    <a href="#" class="text-xs text-[#00635D] hover:underline font-medium flex items-center gap-0.5 normal-case tracking-normal">
                        See all <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </h3>
                <div class="flex flex-wrap gap-x-5 gap-y-1">
                    <a href="#" class="text-sm text-[#00635D] hover:underline font-medium">Want to Read ({{ $shelfCounts['wantToRead'] }})</a>
                    <a href="#" class="text-sm text-[#00635D] hover:underline font-medium">Currently Reading ({{ $shelfCounts['currentlyReading'] }})</a>
                    <a href="#" class="text-sm text-[#00635D] hover:underline font-medium">Read ({{ $shelfCounts['read'] }})</a>
                </div>
            </div>

            <!-- Review List -->
            <div class="bg-white border border-[#DDD8CC] rounded-md p-4 mb-5">
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Recent Reviews</h3>
                @if($reviews->isEmpty())
                    <p class="text-sm text-[#999]">No reviews yet.</p>
                @else
                    <div class="space-y-4">
                        @foreach($reviews as $index => $review)
                            @if($index > 0)
                                <hr class="border-[#EDE9E0] mb-4" />
                            @endif
                            <div class="flex gap-3">
                                <img src="{{ $review->book->cover_url ? (filter_var($review->book->cover_url, FILTER_VALIDATE_URL) ? $review->book->cover_url : Storage::url($review->book->cover_url)) : 'https://placehold.co/48x72?text=No+Cover' }}" alt="{{ $review->book->title }}" class="w-12 h-[72px] rounded shadow-sm shrink-0 object-cover">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('books.show', $review->book) }}" class="text-sm font-bold text-[#382110] hover:text-[#00635D] leading-tight block">{{ $review->book->title }}</a>
                                    <p class="text-xs text-[#888] mb-1.5">by {{ $review->book->authors->first()->name ?? 'Unknown' }}</p>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= ($review->rating?->stars ?? 0) ? 'fill-[#F5A623] text-[#F5A623]' : 'text-[#D8D2C8]' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-xs text-[#555] leading-relaxed line-clamp-3">{{ $review->body }}</p>
                                    <p class="text-xs text-[#AAA] mt-1.5 italic">{{ $review->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Bottom panels -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Genre Panel -->
                <div class="bg-white border border-[#DDD8CC] rounded-md p-4 mb-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Favorite Genres</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($favoriteGenres as $genre)
                            <a href="#" class="px-2.5 py-1 rounded-full border border-[#C8C0B0] text-xs text-[#555] hover:border-[#00635D] hover:text-[#00635D] transition-colors">
                                {{ $genre }}
                            </a>
                        @empty
                            <p class="text-xs text-[#999]">No favorite genres set.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Followers Panel -->
                <div class="bg-white border border-[#DDD8CC] rounded-md p-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Followers</h3>
                    <div class="flex gap-2 mb-3 flex-wrap">
                        @forelse($followers as $follower)
                            <a href="{{ route('profile.show', $follower->username) }}" class="shrink-0" title="{{ $follower->username }}">
                                @if($follower->avatar_url)
                                    <img src="{{ Storage::url($follower->avatar_url) }}" alt="{{ $follower->username }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm hover:border-[#00635D] transition-colors" />
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 border-2 border-white shadow-sm hover:border-[#00635D] transition-colors flex items-center justify-center text-gray-500 font-bold text-sm">
                                        {{ substr($follower->username, 0, 1) }}
                                    </div>
                                @endif
                            </a>
                        @empty
                            <p class="text-xs text-[#999]">No followers yet.</p>
                        @endforelse
                    </div>
                    @if($followers->count() > 0)
                        <a href="{{ route('profile.followers', $user->username) }}" class="text-sm text-[#00635D] hover:underline font-medium">See all followers &raquo;</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const followBtn = document.getElementById('follow-btn');
        if (followBtn) {
            followBtn.addEventListener('click', async function() {
                const userId = this.getAttribute('data-user-id');
                const isFollowing = this.getAttribute('data-is-following') === 'true';
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                              || document.querySelector('input[name="_token"]')?.value;

                // Disable button while processing
                this.disabled = true;
                
                try {
                    const url = isFollowing ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
                    const method = isFollowing ? 'DELETE' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        alert(errorData.error || 'An error occurred.');
                        this.disabled = false;
                        return;
                    }

                    const data = await response.json();

                    if (data.status === 'following') {
                        this.setAttribute('data-is-following', 'true');
                        this.innerText = 'Following';
                        this.className = 'px-4 py-1.5 rounded text-sm font-semibold transition-colors border border-[#C8C0B0] text-[#555] hover:border-[#999] hover:text-[#333]';
                    } else if (data.status === 'unfollowed') {
                        this.setAttribute('data-is-following', 'false');
                        this.innerText = 'Follow';
                        this.className = 'px-4 py-1.5 rounded text-sm font-semibold transition-colors bg-[#5C7A3E] hover:opacity-90 text-white';
                    }
                    
                    // Note: We don't live-update the followers count on this page because it's static PHP text,
                    // but the button toggles instantly. A full refresh would show the new count.
                } catch (err) {
                    console.error(err);
                    alert('Network error.');
                }
                
                this.disabled = false;
            });
        }
    });
</script>
@endsection
