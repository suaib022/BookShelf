@extends('layouts.app')

@section('title', 'My Books — BookShelf')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6" x-data="myBooksPage()">

    {{-- ── Page heading row ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <h1 class="font-serif text-3xl font-bold text-[#382110]">My Books</h1>

        <div class="flex items-center gap-3 flex-wrap">
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
                        <li class="flex items-center justify-between group">
                            <a
                                href="{{ route('my-books.index', ['shelf' => $shelf['slug']]) }}"
                                class="block px-1 py-0.5 rounded transition-colors flex-1 {{ $activeShelf === $shelf['slug'] ? 'font-semibold text-[#382110] bg-[#F4F1EA]' : 'text-[#00635D] hover:underline' }}"
                            >
                                {{ $shelf['name'] }} ({{ $shelf['count'] }})
                            </a>
                            @if($shelf['is_custom'] && $shelf['id'])
                                <form action="{{ route('shelves.destroy', $shelf['id']) }}" method="POST" class="inline" onsubmit="return confirm('{{ $shelf['count'] > 0 ? "There are {$shelf['count']} books in this shelf. " : "" }}Are you sure you want to delete this custom shelf?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Delete custom shelf">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3" x-data="{ adding: false }">
                    <button x-show="!adding" @click="adding = true" class="w-full text-center px-3 py-1.5 text-xs font-semibold border border-[#C8C0B0] text-[#333] rounded hover:bg-[#F4F1EA] transition-colors">
                        Add shelf
                    </button>
                    <form x-show="adding" action="{{ route('shelves.custom.store') }}" method="POST" class="flex flex-col gap-2" style="display: none;" x-cloak>
                        @csrf
                        <input type="text" name="name" placeholder="Shelf name" required class="w-full border border-[#C8C0B0] rounded px-2 py-1 text-sm focus:outline-none focus:border-[#00635D]">
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-[#00635D] text-white text-xs py-1 rounded hover:bg-[#004e4a] transition-colors">Save</button>
                            <button type="button" @click="adding = false" class="flex-1 border border-[#C8C0B0] text-[#333] text-xs py-1 rounded hover:bg-[#F4F1EA] transition-colors">Cancel</button>
                        </div>
                    </form>
                </div>
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

            @if($paginator->count() === 0)
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
                                <span class="block text-[#555] leading-tight">added</span>
                            </th>
                            <th class="py-2 font-semibold text-[#555]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EDE9E0]">
                        @foreach($paginator as $book)
                        <tr class="align-top hover:bg-[#FAFAF7] transition-colors group" x-data="bookRow({{ $book['id'] }}, {{ $book['my_rating'] }})">

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
                                <div class="flex items-center gap-0.5" @mouseleave="hoverRating = 0">
                                    <template x-for="s in 5">
                                        <button
                                            type="button"
                                            @mouseenter="hoverRating = s"
                                            @click="submitRating(s)"
                                            class="transition-transform hover:scale-110"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                 viewBox="0 0 24 24"
                                                 :fill="s <= (hoverRating || currentRating) ? '#fbbf24' : 'none'"
                                                 :stroke="s <= (hoverRating || currentRating) ? '#fbbf24' : '#C8C0B0'"
                                                 stroke-width="1.5">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>

                            {{-- Shelf --}}
                            <td class="py-3 pr-3" x-data="{ editing: false }">
                                <div x-show="!editing">
                                    <span class="text-[#333] whitespace-nowrap">{{ str_replace('-', ' ', $book['shelf_label']) }}</span>
                                    <br>
                                    <button @click="editing = true" class="text-[10px] text-[#00635D] hover:underline">[edit]</button>
                                </div>
                                <div x-show="editing" style="display: none;">
                                    <form action="{{ route('shelves.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book['id'] }}">
                                        <select name="shelf_name" onchange="this.form.submit()" @click.away="editing = false" class="text-xs border border-gray-300 rounded p-1 max-w-[100px]">
                                            <option value="">Select shelf</option>
                                            @foreach($shelves as $s)
                                                @if($s['slug'] !== 'all')
                                                    <option value="{{ $s['name'] }}" {{ $book['shelf_label'] === $s['name'] ? 'selected' : '' }}>{{ $s['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </td>

                            {{-- Review --}}
                            <td class="py-3 pr-3" x-data="{ reviewOpen: false }">
                                @if($book['review'])
                                    <button @click="reviewOpen = true" class="text-[#00635D] hover:underline text-xs">view »</button>
                                    <br>
                                    <button @click="reviewOpen = true" class="text-[10px] text-[#00635D] hover:underline">[edit]</button>
                                @else
                                    <button @click="reviewOpen = true" class="text-[#00635D] hover:underline text-xs whitespace-nowrap">Write a review</button>
                                @endif

                                <!-- Review Modal -->
                                <div x-show="reviewOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display:none;" x-cloak>
                                    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg" @click.away="reviewOpen = false">
                                        <h2 class="text-lg font-bold mb-4 font-serif text-[#382110]">Review: {{ $book['title'] }}</h2>
                                        <form action="{{ route('reviews.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book['id'] }}">
                                            <textarea name="body" class="w-full border border-gray-300 rounded p-3 mb-4 focus:outline-none focus:border-[#00635D]" rows="5" placeholder="What did you think?">{{ $book['review'] }}</textarea>
                                            <div class="flex items-center gap-2 mb-4">
                                                <input type="checkbox" name="contains_spoilers" id="spoilers_{{ $book['id'] }}" value="1" class="accent-[#00635D]">
                                                <label for="spoilers_{{ $book['id'] }}" class="text-sm text-gray-700">Contains spoilers</label>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="reviewOpen = false" class="px-4 py-2 border border-gray-300 text-gray-600 rounded hover:bg-gray-50">Cancel</button>
                                                <button type="submit" class="px-4 py-2 bg-[#00635D] text-white rounded hover:bg-[#004e4a]">Save Review</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
                                <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="{{ route('shelves.remove-book', $book['id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this book from your shelf?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Remove from shelf">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination links ──────────────────────────────────── --}}
            <div class="mt-4">
                {{ $paginator->links() }}
            </div>

            @endif

            {{-- ── Footer controls ───────────────────────────────────── --}}
            <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-[#555] border-t border-[#DDD8CC] pt-4 pb-12">

                {{-- Per page --}}
                <div class="flex items-center gap-2">
                    <label for="per-page-select" class="whitespace-nowrap">per page</label>
                    <!-- Added pr-8 to fix down arrow mixing with text -->
                    <select
                        id="per-page-select"
                        name="per_page"
                        onchange="applyTableFilter()"
                        class="border border-[#C8C0B0] rounded px-2 pr-8 py-1 text-sm bg-white text-[#333] focus:outline-none focus:border-[#00635D] appearance-none"
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
                        class="border border-[#C8C0B0] rounded px-2 pr-8 py-1 text-sm bg-white text-[#333] focus:outline-none focus:border-[#00635D] appearance-none"
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
document.addEventListener('alpine:init', () => {
    Alpine.data('myBooksPage', () => ({
        applyTableFilter() {
            window.applyTableFilter();
        }
    }));

    Alpine.data('bookRow', (bookId, initialRating) => ({
        bookId: bookId,
        currentRating: initialRating,
        hoverRating: 0,
        submitRating(stars) {
            this.currentRating = stars;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route("ratings.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ book_id: this.bookId, stars: stars }),
            })
            .then(r => r.json())
            .then(data => {
                // Flash success manually if needed, or just let UI show updated stars
            })
            .catch(err => {
                console.error(err);
                alert("Failed to submit rating.");
            });
        }
    }));
});

function applyTableFilter() {
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', document.getElementById('per-page-select').value);
    params.set('sort',     document.getElementById('sort-select').value);
    const dir = document.querySelector('input[name="dir"]:checked');
    if (dir) params.set('dir', dir.value);
    window.location.search = params.toString();
}
</script>

<style>
/* ensure modals can sit on top */
[x-cloak] { display: none !important; }

/* default select arrow overrides to fix mixed arrow with text */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
}
</style>
@endsection
