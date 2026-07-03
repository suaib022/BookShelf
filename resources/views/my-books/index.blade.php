@extends('layouts.app')

@section('title', 'My Books — BookShelf')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

    {{-- ── Page heading row ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <h1 class="font-serif text-3xl font-bold text-[#382110]">My Books</h1>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- Search & add books input --}}
            <form action="{{ route('books.index') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="q"
                    placeholder="Search and add books"
                    class="pl-3 pr-8 py-1 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#999] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition w-48"
                />
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-[#888]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>

            {{-- Utility text links --}}
            <div class="flex items-center gap-3 text-sm text-[#00635D]">
                <a href="#" class="hover:underline">Batch Edit</a>
                <a href="#" class="hover:underline">Settings</a>
                <a href="#" class="hover:underline">Stats</a>
                <a href="#" class="hover:underline">Print</a>
            </div>

            {{-- View toggle icons --}}
            <div class="flex items-center border border-[#C8C0B0] rounded overflow-hidden">
                {{-- List view (default / active) --}}
                <button
                    title="List view"
                    class="px-2 py-1.5 bg-[#00635D] text-white transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
                {{-- Grid view --}}
                <button
                    title="Grid view"
                    class="px-2 py-1.5 bg-white text-[#555] hover:bg-[#F4F1EA] transition-colors"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Two-column layout ────────────────────────────────────────────── --}}
    <div class="flex gap-6">

        {{-- ─── Left Sidebar ──────────────────────────────────────────── --}}
        <aside class="w-[220px] flex-shrink-0 text-sm">

            {{-- Bookshelves --}}
            <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-[#382110]">Bookshelves</span>
                    <a href="#" class="text-[#00635D] hover:underline text-xs">(Edit)</a>
                </div>
                <ul class="space-y-0.5">
                    @foreach($shelves as $shelf)
                        <li>
                            <a
                                href="{{ route('my-books.index', ['shelf' => $shelf['slug']]) }}"
                                class="block px-1 py-0.5 rounded transition-colors {{ $activeShelf === $shelf['slug'] ? 'font-semibold text-[#382110] bg-[#F4F1EA]' : 'text-[#00635D] hover:underline' }}"
                            >
                                {{ $shelf['name'] }} ({{ $shelf['count'] }})
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3">
                    <button class="w-full text-center px-3 py-1.5 text-xs font-semibold border border-[#C8C0B0] text-[#333] rounded hover:bg-[#F4F1EA] transition-colors">
                        Add shelf
                    </button>
                </div>
            </div>

            <hr class="border-[#DDD8CC] mb-5"/>

            {{-- Your reading activity --}}
            <div class="mb-5">
                <p class="font-bold text-[#382110] mb-2">Your reading activity</p>
                <ul class="space-y-1">
                    <li><a href="#" class="text-[#00635D] hover:underline">Reading Challenge</a></li>
                    <li><a href="#" class="text-[#00635D] hover:underline">Reading stats</a></li>
                </ul>
            </div>

            <hr class="border-[#DDD8CC] mb-5"/>

            {{-- Add books --}}
            <div class="mb-5">
                <p class="font-bold text-[#382110] mb-2">Add books</p>
                <ul class="space-y-1">
                    <li><a href="#" class="text-[#00635D] hover:underline">Recommendations</a></li>
                    <li><a href="{{ route('books.index') }}" class="text-[#00635D] hover:underline">Explore</a></li>
                </ul>
            </div>

        </aside>

        {{-- ─── Main content area ─────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            @if(count($books) === 0)
                <p class="text-[#888] italic text-sm py-8 text-center">No books on this shelf yet.</p>
            @else

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="text-left border-b border-[#DDD8CC]">
                            <th class="py-2 pr-3 font-semibold text-[#555] w-12">cover</th>
                            <th class="py-2 pr-3 font-semibold text-[#555]">title</th>
                            <th class="py-2 pr-3 font-semibold text-[#555]">author</th>
                            <th class="py-2 pr-3 font-semibold text-[#555] whitespace-nowrap">
                                <span class="block text-[#555] leading-tight">avg</span>
                                <span class="block text-[#555] leading-tight">rating</span>
                            </th>
                            <th class="py-2 pr-3 font-semibold text-[#555]">rating</th>
                            <th class="py-2 pr-3 font-semibold text-[#555]">shelves</th>
                            <th class="py-2 pr-3 font-semibold text-[#555]">review</th>
                            <th class="py-2 pr-3 font-semibold text-[#555] whitespace-nowrap">
                                <span class="block text-[#555] leading-tight">date</span>
                                <span class="block text-[#555] leading-tight">read</span>
                            </th>
                            <th class="py-2 pr-3 font-semibold text-[#555] whitespace-nowrap">
                                <span class="block text-[#555] leading-tight">date</span>
                                <span class="block text-[#555] leading-tight">added ▾</span>
                            </th>
                            <th class="py-2 font-semibold text-[#555]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EDE9E0]">
                        @foreach($books as $book)
                        <tr class="align-top hover:bg-[#FAFAF7] transition-colors group">

                            {{-- Cover --}}
                            <td class="py-3 pr-3 w-12">
                                <a href="{{ route('books.show', $book['id']) }}">
                                    @if($book['cover'])
                                        <img
                                            src="{{ $book['cover'] }}"
                                            alt="{{ $book['title'] }}"
                                            class="w-10 rounded shadow-sm object-cover"
                                            style="aspect-ratio: 2/3;"
                                        />
                                    @else
                                        <div class="w-10 bg-gray-200 rounded flex items-center justify-center text-[10px] text-gray-400 text-center" style="aspect-ratio:2/3;">No cover</div>
                                    @endif
                                </a>
                            </td>

                            {{-- Title --}}
                            <td class="py-3 pr-3 max-w-[200px]">
                                <a href="{{ route('books.show', $book['id']) }}"
                                   class="font-bold text-[#333] hover:text-[#00635D] hover:underline leading-snug">
                                    {{ $book['title'] }}
                                </a>
                            </td>

                            {{-- Author --}}
                            <td class="py-3 pr-3 whitespace-nowrap">
                                <a href="{{ route('books.index', ['q' => $book['author']]) }}"
                                   class="text-[#00635D] hover:underline">
                                    {{ $book['author'] }}
                                </a>
                            </td>

                            {{-- Avg rating --}}
                            <td class="py-3 pr-3 whitespace-nowrap text-[#555]">
                                {{ number_format($book['avg_rating'], 2) }}
                            </td>

                            {{-- My rating (interactive stars) --}}
                            <td class="py-3 pr-3">
                                <div class="flex items-center gap-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                        <button
                                            type="button"
                                            title="{{ $s }} star{{ $s > 1 ? 's' : '' }}"
                                            class="transition-transform hover:scale-110"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                 viewBox="0 0 24 24"
                                                 fill="{{ $s <= ($book['my_rating'] ?? 0) ? '#fbbf24' : 'none' }}"
                                                 stroke="{{ $s <= ($book['my_rating'] ?? 0) ? '#fbbf24' : '#C8C0B0' }}"
                                                 stroke-width="1.5">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                            </td>

                            {{-- Shelf --}}
                            <td class="py-3 pr-3">
                                <span class="text-[#333]">{{ str_replace('-', ' ', $book['shelf_label']) }}</span>
                                <br>
                                <a href="#" class="text-[10px] text-[#00635D] hover:underline">[edit]</a>
                            </td>

                            {{-- Review --}}
                            <td class="py-3 pr-3">
                                @if($book['review'])
                                    <a href="#" class="text-[#00635D] hover:underline text-xs">view »</a>
                                    <br>
                                    <a href="#" class="text-[10px] text-[#00635D] hover:underline">[edit]</a>
                                @else
                                    <a href="{{ route('books.show', $book['id']) }}" class="text-[#00635D] hover:underline text-xs whitespace-nowrap">Write a review</a>
                                    <br>
                                    <a href="#" class="text-[10px] text-[#00635D] hover:underline">[edit]</a>
                                @endif
                            </td>

                            {{-- Date read --}}
                            <td class="py-3 pr-3 text-[#555] whitespace-nowrap text-xs">
                                {{ $book['date_read'] ?? 'not set' }}
                            </td>

                            {{-- Date added --}}
                            <td class="py-3 pr-3 text-[#555] whitespace-nowrap text-xs">
                                @if($book['date_added'])
                                    {{ \Carbon\Carbon::parse($book['date_added'])->format('M j, Y') }}
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('books.show', $book['id']) }}"
                                       class="text-xs text-[#00635D] hover:underline">edit</a>
                                    <button
                                        type="button"
                                        title="Remove from shelf"
                                        class="text-[#888] hover:text-red-500 transition-colors ml-0.5"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @endif

            {{-- ── Footer controls ───────────────────────────────────── --}}
            <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-[#555] border-t border-[#DDD8CC] pt-4">

                {{-- Per page --}}
                <div class="flex items-center gap-2">
                    <label for="per-page-select" class="whitespace-nowrap">per page</label>
                    <select
                        id="per-page-select"
                        name="per_page"
                        onchange="applyTableFilter()"
                        class="border border-[#C8C0B0] rounded px-2 py-1 text-sm bg-white text-[#333] focus:outline-none focus:border-[#00635D]"
                    >
                        @foreach([10, 20, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div class="flex items-center gap-2">
                    <label for="sort-select">sort</label>
                    <select
                        id="sort-select"
                        name="sort"
                        onchange="applyTableFilter()"
                        class="border border-[#C8C0B0] rounded px-2 py-1 text-sm bg-white text-[#333] focus:outline-none focus:border-[#00635D]"
                    >
                        <option value="title"      {{ $sort === 'title'      ? 'selected' : '' }}>Title</option>
                        <option value="author"     {{ $sort === 'author'     ? 'selected' : '' }}>Author</option>
                        <option value="date_added" {{ $sort === 'date_added' ? 'selected' : '' }}>Date Added</option>
                        <option value="date_read"  {{ $sort === 'date_read'  ? 'selected' : '' }}>Date Read</option>
                        <option value="avg_rating" {{ $sort === 'avg_rating' ? 'selected' : '' }}>Rating</option>
                    </select>
                </div>

                {{-- Asc / Desc --}}
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="dir" value="asc" id="dir-asc"
                               {{ $dir === 'asc' ? 'checked' : '' }}
                               onchange="applyTableFilter()"
                               class="accent-[#00635D]" />
                        asc.
                    </label>
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="dir" value="desc" id="dir-desc"
                               {{ $dir === 'desc' ? 'checked' : '' }}
                               onchange="applyTableFilter()"
                               class="accent-[#00635D]" />
                        desc.
                    </label>
                </div>
            </div>
        </div>{{-- /main --}}
    </div>{{-- /two-column --}}
</div>

<script>
function applyTableFilter() {
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', document.getElementById('per-page-select').value);
    params.set('sort',     document.getElementById('sort-select').value);
    const dir = document.querySelector('input[name="dir"]:checked');
    if (dir) params.set('dir', dir.value);
    window.location.search = params.toString();
}
</script>
@endsection
