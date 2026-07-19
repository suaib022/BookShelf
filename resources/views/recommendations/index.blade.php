@extends('layouts.app')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 py-8">
    <div class="mb-8 border-b border-[#DDD8CC] pb-4">
        <h1 class="text-3xl font-bold text-[#382110]">Recommended for You</h1>
        <p class="text-[#555] mt-2">Books we think you'll love, based on what you've rated highly.</p>
    </div>

    @if($recommendations->isEmpty())
        <div class="bg-white border border-[#DDD8CC] rounded-md p-10 text-center">
            <p class="text-[#666] text-lg mb-2">We don't have enough data to recommend books for you yet.</p>
            <p class="text-[#888]">Rate some books 4 or 5 stars, and we'll generate recommendations for you!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommendations as $rec)
                @php $book = $books[$rec->id]; @endphp
                <div class="bg-white border border-[#DDD8CC] rounded-md flex flex-col hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-5 flex gap-4">
                        <a href="{{ route('books.show', $book) }}" class="shrink-0">
                            <img src="{{ $book->cover_url ? (filter_var($book->cover_url, FILTER_VALIDATE_URL) ? $book->cover_url : Storage::url($book->cover_url)) : 'https://placehold.co/80x120?text=No+Cover' }}" alt="{{ $book->title }}" class="w-[84px] h-[126px] object-cover rounded shadow-md">
                        </a>
                        <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
                            <div>
                                <a href="{{ route('books.show', $book) }}" class="font-bold text-[#382110] hover:text-[#00635D] text-[17px] leading-snug line-clamp-2 mb-1">{{ $book->title }}</a>
                                <p class="text-[13px] text-[#666]">by {{ $book->authors->first()->name ?? 'Unknown' }}</p>
                                
                                <div class="flex items-center gap-1 mt-2">
                                    <span class="text-sm font-bold text-[#333]">{{ number_format($book->avg_rating, 2) }}</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= round($book->avg_rating) ? 'fill-[#F5A623] text-[#F5A623]' : 'text-[#D8D2C8]' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-[#888] ml-1">({{ $book->ratings_count }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-[#FBF9F3] border-t border-[#E8E2D4] p-3 mt-auto rounded-b-md text-xs text-[#555] font-medium text-center">
                        {{ $rec->reason }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $recommendations->links() }}
        </div>
    @endif
</div>
@endsection
