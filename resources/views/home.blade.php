@extends('layouts.app')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 py-6">
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        @auth
        <!-- Left Sidebar (Logged In) -->
        <aside class="w-full lg:w-[300px] shrink-0">
            <div class="bg-white border border-[#DDD8CC] rounded-md overflow-hidden">
                <!-- Currently Reading -->
                <div class="py-4">
                    <div class="px-4">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Currently Reading</h3>
                        @if($currentlyReadingBooks->isNotEmpty())
                            @foreach($currentlyReadingBooks as $shelfBook)
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ $shelfBook->book->cover_url ? (filter_var($shelfBook->book->cover_url, FILTER_VALIDATE_URL) ? $shelfBook->book->cover_url : Storage::url($shelfBook->book->cover_url)) : 'https://placehold.co/40x48?text=No+Cover' }}" class="w-10 h-12 object-cover rounded shrink-0 shadow-sm" alt="Cover">
                                <div>
                                    <a href="{{ route('books.show', $shelfBook->book) }}" class="text-sm font-bold text-[#382110] hover:text-[#00635D] leading-tight block">{{ $shelfBook->book->title }}</a>
                                    <p class="text-xs text-[#777] mt-0.5">by {{ $shelfBook->book->authors->first()->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-12 bg-[#E8E4DC] rounded flex items-center justify-center shrink-0">
                                    <svg class="text-[#999] w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <p class="text-sm text-[#666] italic">What are you reading?</p>
                            </div>
                        @endif
                        <form action="{{ route('books.index') }}" method="GET" class="relative mb-3">
                            <input type="text" name="q" placeholder="Search books" class="w-full pl-3 pr-8 py-1.5 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                            <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#888]">
                                <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
                        <div class="flex gap-3 text-xs">
                            <a href="{{ route('recommendations.index') }}" class="text-[#00635D] hover:underline font-medium">Recommendations</a>
                        </div>
                    </div>
                </div>

                <hr class="border-[#DDD8CC]" />

                <!-- Reading Challenge -->
                <!-- <div class="py-4">
                    <div class="px-4">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Reading Challenge</h3>
                        <div class="flex items-center gap-3">
                            <div class="bg-[#1B2A4A] text-white rounded px-3 py-2 text-center shrink-0 leading-tight">
                                <div class="text-lg font-black tracking-tight">{{ date('Y') }}</div>
                                <div class="text-[8px] font-bold uppercase tracking-widest opacity-80 leading-tight">Reading<br />Challenge</div>
                            </div>
                            <button class="text-xs border border-[#C8C0B0] text-[#555] rounded px-3 py-1.5 hover:bg-[#F4F1EA] hover:border-[#999] transition-colors font-medium">
                                Start Challenge
                            </button>
                        </div>
                    </div>
                </div> -->

                <hr class="border-[#DDD8CC]" />

                <!-- Want to Read -->
                <div class="py-4">
                    <div class="px-4">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Want to Read</h3>
                        @if($wantToReadBooks->isNotEmpty())
                            @foreach($wantToReadBooks->take(3) as $shelfBook)
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ $shelfBook->book->cover_url ? (filter_var($shelfBook->book->cover_url, FILTER_VALIDATE_URL) ? $shelfBook->book->cover_url : Storage::url($shelfBook->book->cover_url)) : 'https://placehold.co/40x48?text=No+Cover' }}" class="w-10 h-12 object-cover rounded shrink-0 shadow-sm" alt="Cover">
                                    <div class="min-w-0">
                                        <a href="{{ route('books.show', $shelfBook->book) }}" class="text-sm font-bold text-[#382110] hover:text-[#00635D] leading-tight block truncate">{{ $shelfBook->book->title }}</a>
                                        <p class="text-xs text-[#777] mt-0.5">by {{ $shelfBook->book->authors->first()->name ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-12 bg-[#E8E4DC] rounded flex items-center justify-center shrink-0">
                                    <svg class="text-[#999] w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                </div>
                                <p class="text-sm text-[#666] italic">What do you want to read next?</p>
                            </div>
                        @endif
                        <div class="flex gap-3 text-xs mb-1">
                            <a href="{{ route('recommendations.index') }}" class="text-[#00635D] hover:underline font-medium">Recommendations</a>
                        </div>
                    </div>
                </div>

                <hr class="border-[#DDD8CC]" />

                <!-- My Books -->
                <div class="py-4">
                    <div class="px-4">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">My Books</h3>
                        <ul class="space-y-1.5">
                            <li class="flex items-center gap-2 text-sm">
                                <span class="text-[#00635D] font-semibold w-5 text-right tabular-nums">{{ $shelfCounts['Want to Read'] }}</span>
                                <a href="{{ route('my-books.index', ['shelf' => 'want-to-read']) }}" class="text-[#00635D] hover:underline">Want to Read</a>
                            </li>
                            <li class="flex items-center gap-2 text-sm">
                                <span class="text-[#00635D] font-semibold w-5 text-right tabular-nums">{{ $shelfCounts['Currently Reading'] }}</span>
                                <a href="{{ route('my-books.index', ['shelf' => 'currently-reading']) }}" class="text-[#00635D] hover:underline">Currently Reading</a>
                            </li>
                            <li class="flex items-center gap-2 text-sm">
                                <span class="text-[#00635D] font-semibold w-5 text-right tabular-nums">{{ $shelfCounts['Read'] }}</span>
                                <a href="{{ route('my-books.index', ['shelf' => 'read']) }}" class="text-[#00635D] hover:underline">Read</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>
        @else
        <!-- Left Sidebar (Guest) -->
        <aside class="w-full lg:w-[300px] shrink-0">
            <div class="bg-white border border-[#DDD8CC] rounded-md p-4 text-center">
                <h3 class="font-bold text-[#382110] mb-2">Discover New Books</h3>
                <p class="text-sm text-[#666] mb-4">Sign up to track your reading, review books, and see what your friends are reading.</p>
                <a href="{{ route('register') }}" class="block w-full bg-black text-white font-bold text-sm rounded py-2 hover:bg-gray-800 transition">Create Account</a>
                <p class="text-xs text-[#999] mt-3">Already have an account? <a href="{{ route('login') }}" class="text-[#00635D] hover:underline">Sign in</a></p>
            </div>
        </aside>
        @endauth

        <!-- Center Feed -->
        <main class="flex-1 min-w-0">
            <!-- Picks banner -->
            <div class="bg-white border border-[#DDD8CC] rounded-md overflow-hidden mb-5">
                <div class="flex flex-col sm:flex-row">
                    <img src="https://images.pexels.com/photos/1370295/pexels-photo-1370295.jpeg?auto=compress&cs=tinysrgb&w=600&h=240&dpr=1" alt="Staff picks" class="w-full sm:w-[240px] h-40 sm:h-auto object-cover shrink-0">
                    <div class="p-5 flex flex-col justify-center">
                        <h2 class="text-xl font-bold text-[#382110] mb-1">Staff Picks: {{ date('Y') }}</h2>
                        <p class="text-sm text-[#666] leading-relaxed">Our editors' favorite reads — from quiet literary fiction to bold speculative worlds.</p>
                    </div>
                </div>
            </div>

            @auth
            <!-- Activity header -->
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-[11px] font-bold uppercase tracking-widest text-[#555]">Activity</h2>
                <button class="p-1 rounded hover:bg-[#E8E2D4] text-[#888] hover:text-[#555] transition-colors">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
            </div>

            <!-- Activity feed -->
            <div class="space-y-3">
                @if($activityEvents->isNotEmpty())
                    @foreach($activityEvents as $event)
                    @php
                        $meta = $event->metadata ?? [];
                        $hasStars = isset($meta['stars']);
                        $hasBody = isset($meta['body']);
                        $hasBoth = $hasStars && $hasBody;
                        $actionWord = $hasBoth 
                            ? ($event->type === 'review' ? 'reviewed' : 'rated') 
                            : ($event->type === 'review' ? 'reviewed' : 'rated');
                    @endphp
                    <article class="bg-white border border-[#DDD8CC] rounded-md p-5 flex flex-col gap-3">
                        <div class="text-[13px] text-[#777]">
                            <span class="font-bold text-[#555]">{{ $event->user->username }}</span> 
                            @if(\Carbon\Carbon::parse($event->created_at)->diffInDays(now()) < 3)
                                recently {{ $hasBoth ? 'rated and reviewed' : $actionWord }} a book
                            @else
                                {{ $hasBoth ? 'rated and reviewed' : $actionWord }} a book
                            @endif
                        </div>
                        
                        @if($event->book)
                        <div class="flex gap-4">
                            <img src="{{ $event->book->cover_url ? (filter_var($event->book->cover_url, FILTER_VALIDATE_URL) ? $event->book->cover_url : Storage::url($event->book->cover_url)) : 'https://placehold.co/64x96?text=No+Cover' }}" alt="{{ $event->book->title }}" class="object-cover border border-[#e8e0d5] shadow-sm shrink-0" style="width: 60px; height: 100px;">
                            
                            <div class="flex-1 min-w-0 flex flex-col">
                                <a href="{{ route('books.show', $event->book) }}" class="text-xl font-bold text-[#382110] hover:text-[#00635D] leading-tight mb-1">{{ $event->book->title }}</a>
                                <p class="text-sm text-[#777] mb-2">by {{ $event->book->authors->first()->name ?? 'Unknown' }}</p>
                                
                                @if($hasStars)
                                <div class="flex items-center gap-1 mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $meta['stars'] ? 'fill-[#F5A623] text-[#F5A623]' : 'fill-[#D8D2C8] text-[#D8D2C8]' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    @endfor
                                </div>
                                @endif
                                
                                @if($hasBody)
                                <p class="text-[#555] text-sm leading-relaxed mb-3">
                                    {{ $meta['body'] }}
                                </p>
                                @endif
                                
                                <div class="mt-auto pt-1 text-sm text-[#999] italic">
                                    {{ \Carbon\Carbon::parse($event->created_at)->format('M d, Y') }} 
                                    <span class="not-italic text-[#777]"> — {{ $hasBoth ? 'rated & reviewed' : ($event->type === 'review' ? 'reviewed' : 'rated') }} by <a href="{{ route('profile.show', $event->user->username) }}" class="font-bold text-[#555] hover:underline">{{ $event->user->username }}</a></span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </article>
                    @endforeach
                    
                    <div class="py-4">
                        {{ $activityEvents->links() }}
                    </div>
                @else
                    <div class="bg-white border border-[#DDD8CC] rounded-md p-8 text-center text-[#666]">
                        <p>No activity yet. Follow some users to see their updates here!</p>
                    </div>
                @endif
            </div>
            @else
            <!-- Guest Feed -->
            <div class="space-y-3">
                <div class="bg-white border border-[#DDD8CC] rounded-md p-8 text-center text-[#666]">
                    <h3 class="text-xl font-bold text-[#382110] mb-2">Welcome to BookShelf</h3>
                    <p class="mb-4">Log in or create an account to start tracking your reading journey and connecting with others.</p>
                </div>
            </div>
            @endauth
        </main>

        <!-- Right Sidebar -->
        <aside class="w-full lg:w-[300px] shrink-0">
            <div class="bg-white border border-[#DDD8CC] rounded-md overflow-hidden">
                <div class="p-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Discover</h3>
                    <p class="text-xs text-[#666] mb-3 leading-relaxed">
                        Find your next favorite book by browsing our genres and recommendations.
                    </p>
                    <a href="{{ route('books.index') }}" class="block w-full text-center text-xs border border-[#C8C0B0] text-[#555] rounded px-3 py-1.5 hover:bg-[#F4F1EA] hover:border-[#999] transition-colors font-medium">
                        Browse Catalog
                    </a>
                </div>
                
                <hr class="border-[#DDD8CC]" />
                
                @auth
                @if(isset($recommendations) && $recommendations->isNotEmpty())
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555]">Recommended for You</h3>
                        <a href="{{ route('recommendations.index') }}" class="text-[10px] text-[#00635D] hover:underline uppercase tracking-widest font-bold">See all</a>
                    </div>
                    <div class="space-y-4">
                        @foreach($recommendations as $rec)
                        <div class="flex items-start gap-3">
                            <a href="{{ route('books.show', $rec->book) }}" class="shrink-0">
                                <img src="{{ $rec->book->cover_url ? (filter_var($rec->book->cover_url, FILTER_VALIDATE_URL) ? $rec->book->cover_url : Storage::url($rec->book->cover_url)) : 'https://placehold.co/48x72?text=No+Cover' }}" class="w-12 h-[72px] object-cover rounded shadow-sm" alt="Cover">
                            </a>
                            <div class="min-w-0">
                                <a href="{{ route('books.show', $rec->book) }}" class="text-sm font-bold text-[#382110] hover:text-[#00635D] leading-tight block">{{ $rec->book->title }}</a>
                                <p class="text-[10px] text-[#888] mt-0.5 line-clamp-2 leading-snug">{{ $rec->reason }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <hr class="border-[#DDD8CC]" />
                @endif
                <div class="p-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555] mb-3">Improve Recommendations</h3>
                    <p class="text-xs text-[#666] mb-3 leading-relaxed">
                        Rate more books to help us understand your taste.
                    </p>
                    <div class="w-full bg-[#E8E2D4] rounded-full h-2 overflow-hidden mb-3">
                        <div class="bg-[#5C7A3E] h-full rounded-full" style="width: 45%;"></div>
                    </div>
                    <a href="{{ route('books.index') }}" class="text-xs text-[#00635D] hover:underline font-medium">
                        Rate more books
                    </a>
                </div>
                @endauth
            </div>
        </aside>
        
    </div>
</div>
@endsection
