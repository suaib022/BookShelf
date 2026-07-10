@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">
    <div class="mb-6 pb-4 border-b border-[#E8E6E0] flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[#382110]">{{ $user->name }}'s Followers</h1>
        <a href="{{ route('profile.show', $user->username) }}" class="text-sm text-[#00635D] hover:underline">&laquo; Back to profile</a>
    </div>

    @if($followers->isEmpty())
        <p class="text-[#555]">No followers yet.</p>
    @else
        <div class="space-y-4">
            @foreach($followers as $follower)
                <div class="flex items-center justify-between p-4 bg-white border border-[#E8E6E0] rounded-md shadow-sm">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('profile.show', $follower->username) }}">
                            @if($follower->avatar_url)
                                <img src="{{ Storage::url($follower->avatar_url) }}" alt="{{ $follower->username }}" class="w-12 h-12 rounded-full object-cover border border-[#E8E6E0]">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 border border-[#E8E6E0] flex items-center justify-center text-gray-500 font-bold">
                                    {{ substr($follower->username, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <div>
                            <a href="{{ route('profile.show', $follower->username) }}" class="font-bold text-[#382110] hover:text-[#00635D] leading-tight block">
                                {{ $follower->name }}
                            </a>
                            <p class="text-xs text-[#888]">{{ '@' . $follower->username }}</p>
                        </div>
                    </div>
                    @if(auth()->check() && auth()->id() !== $follower->id)
                        @php
                            $isFollowing = auth()->user()->following()->where('followee_id', $follower->id)->exists();
                        @endphp
                        <button class="list-follow-btn px-3 py-1 rounded text-xs font-semibold transition-colors {{ $isFollowing ? 'border border-[#C8C0B0] text-[#555] hover:border-[#999] hover:text-[#333]' : 'bg-[#5C7A3E] hover:opacity-90 text-white' }}"
                                data-user-id="{{ $follower->id }}"
                                data-is-following="{{ $isFollowing ? 'true' : 'false' }}">
                            {{ $isFollowing ? 'Following' : 'Follow' }}
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $followers->links() }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btns = document.querySelectorAll('.list-follow-btn');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                      || document.querySelector('input[name="_token"]')?.value;

        btns.forEach(btn => {
            btn.addEventListener('click', async function() {
                const userId = this.getAttribute('data-user-id');
                const isFollowing = this.getAttribute('data-is-following') === 'true';
                
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
                        this.className = 'list-follow-btn px-3 py-1 rounded text-xs font-semibold transition-colors border border-[#C8C0B0] text-[#555] hover:border-[#999] hover:text-[#333]';
                    } else if (data.status === 'unfollowed') {
                        this.setAttribute('data-is-following', 'false');
                        this.innerText = 'Follow';
                        this.className = 'list-follow-btn px-3 py-1 rounded text-xs font-semibold transition-colors bg-[#5C7A3E] hover:opacity-90 text-white';
                    }
                } catch (err) {
                    console.error(err);
                    alert('Network error.');
                }
                
                this.disabled = false;
            });
        });
    });
</script>
@endsection
