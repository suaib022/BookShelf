@extends('layouts.app')

@section('content')
<div class="max-w-[700px] mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-[#382110] mb-6">Updates</h1>

    @if($events->isEmpty())
        <div class="bg-white border border-[#DDD8CC] rounded-md p-6 text-center text-[#555]">
            <p>Your feed is empty.</p>
            <p class="text-sm text-[#888] mt-2">Follow some people to see their activity here!</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($events as $event)
                <div class="bg-white border border-[#DDD8CC] rounded-md p-4">
                    <div class="flex gap-3">
                        <a href="{{ route('profile.show', $event->user->username) }}" class="shrink-0">
                            @if($event->user->avatar_url)
                                <img src="{{ filter_var($event->user->avatar_url, FILTER_VALIDATE_URL) ? $event->user->avatar_url : Storage::url($event->user->avatar_url) }}" alt="{{ $event->user->username }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 border-2 border-white shadow-sm flex items-center justify-center text-gray-500 font-bold text-sm">
                                    {{ substr($event->user->username, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-[#382110]">
                                <a href="{{ route('profile.show', $event->user->username) }}" class="font-bold hover:text-[#00635D]">{{ $event->user->name }}</a>
                                
                                @if($event->type === 'shelve')
                                    shelved <a href="{{ route('books.show', $event->book) }}" class="font-bold hover:text-[#00635D] italic">{{ $event->book->title }}</a> to <span class="font-semibold">{{ $event->metadata['shelf'] ?? 'a shelf' }}</span>
                                @elseif($event->type === 'rate')
                                    rated <a href="{{ route('books.show', $event->book) }}" class="font-bold hover:text-[#00635D] italic">{{ $event->book->title }}</a>
                                    <span class="text-[#F5A623] ml-1 tracking-widest text-base">
                                        @for($i = 1; $i <= 5; $i++)
                                            {!! $i <= ($event->metadata['stars'] ?? 0) ? '&#9733;' : '&#9734;' !!}
                                        @endfor
                                    </span>
                                @elseif($event->type === 'review')
                                    reviewed <a href="{{ route('books.show', $event->book) }}" class="font-bold hover:text-[#00635D] italic">{{ $event->book->title }}</a>
                                @elseif($event->type === 'follow' && $event->targetUser)
                                    started following <a href="{{ route('profile.show', $event->targetUser->username) }}" class="font-bold hover:text-[#00635D]">{{ $event->targetUser->name }}</a>
                                @endif
                            </p>
                            
                            <p class="text-xs text-[#AAA] mt-1">{{ $event->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
