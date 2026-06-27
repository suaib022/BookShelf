<x-app-layout>
    <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-8">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 mb-3">
          <a href="{{ route('books.index') }}" class="hover:underline">Browse</a>
          <span class="mx-1.5">›</span>
          <span class="text-gray-700">
            @if($genre)
                {{ $genre->name }}
            @elseif($search)
                Search results for "{{ $search }}"
            @else
                All Books
            @endif
          </span>
        </nav>

        <!-- Heading -->
        <h1 class="font-serif text-4xl font-bold text-gray-800 mb-2">
            @if($genre)
                {{ $genre->name }}
            @elseif($search)
                Search results for "{{ $search }}"
            @else
                Browse
            @endif
        </h1>
        
        @if($genre)
          <p class="text-gray-600 mb-6 max-w-2xl">
            {{ $genre->description ?? 'Explore ' . strtolower($genre->name) . ' books — from epic sagas to hidden gems, discover your next great read.' }}
          </p>
        @else
          <div class="mb-6"></div>
        @endif

        <div class="flex gap-8">
          <!-- Left Sidebar - Filters -->
          <aside class="w-[220px] flex-shrink-0">
            <form method="GET" action="{{ url()->current() }}" class="card p-5 sticky top-24 bg-white rounded shadow-sm border border-gray-100">
              <!-- Keep search input hidden if present so it persists when filtering -->
              @if($search)
                  <input type="hidden" name="search" value="{{ $search }}">
              @endif

              <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Filter</h3>
              <div class="mb-5">
                <label class="text-sm font-semibold text-gray-700 block mb-1.5">Sort by</label>
                <div class="relative">
                  <select
                    name="sort"
                    onchange="this.form.submit()"
                    class="w-full appearance-none border border-[#e8e0d5] rounded px-3 py-2 text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c6138]"
                  >
                    <option value="Title" {{ $sort === 'Title' ? 'selected' : '' }}>Title</option>
                    <option value="Newest" {{ $sort === 'Newest' ? 'selected' : '' }}>Newest</option>
                    <option value="Highest Rated" {{ $sort === 'Highest Rated' ? 'selected' : '' }}>Highest Rated</option>
                  </select>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"><path d="m6 9 6 6 6-6"/></svg>
                </div>
              </div>
              
              @if(!$genre)
                <div>
                  <label class="text-sm font-semibold text-gray-700 block mb-2">Genres</label>
                  <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    @foreach($allGenres as $g)
                      <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-gray-800">
                        <input
                          type="checkbox"
                          name="genre_id[]"
                          value="{{ $g->id }}"
                          onchange="this.form.submit()"
                          {{ is_array(request('genre_id')) && in_array($g->id, request('genre_id')) ? 'checked' : '' }}
                          class="accent-[#3c6138]"
                        />
                        {{ $g->name }}
                      </label>
                    @endforeach
                  </div>
                </div>
              @endif
              
              <!-- Fallback submit button for non-JS users -->
              <noscript>
                <button type="submit" class="mt-4 w-full bg-[#3c6138] text-white py-2 rounded text-sm hover:bg-[#2a4527]">Apply</button>
              </noscript>
            </form>
          </aside>

          <!-- Main Content -->
          <div class="flex-1">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
              @foreach($books as $book)
                <a href="{{ route('books.show', $book) }}" class="group cursor-pointer block">
                  <div class="aspect-[2/3] rounded overflow-hidden shadow-md transition-all duration-200 group-hover:shadow-xl group-hover:-translate-y-1 group-hover:scale-[1.02] bg-gray-100">
                    @if($book->cover_image)
                        <img
                          src="{{ filter_var($book->cover_image, FILTER_VALIDATE_URL) ? $book->cover_image : Storage::url($book->cover_image) }}"
                          alt="{{ $book->title }}"
                          class="w-full h-full object-cover"
                        />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs text-center p-2 bg-gray-50 border border-gray-200">{{ $book->title }}</div>
                    @endif
                  </div>
                  <h3 class="mt-2 font-bold text-sm text-gray-800 leading-snug line-clamp-2 group-hover:text-[#00635d]">
                    {{ $book->title }}
                  </h3>
                  <p class="text-xs text-gray-500 mt-0.5">
                    {{ $book->authors->pluck('name')->join(', ') }}
                  </p>
                  <div class="flex items-center gap-1 mt-1">
                    <div class="flex">
                      @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= round($book->avg_rating) ? '#fbbf24' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= round($book->avg_rating) ? 'text-amber-400' : 'text-gray-300 fill-gray-200' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                      @endfor
                    </div>
                    <span class="text-xs text-gray-500 ml-0.5">{{ number_format($book->avg_rating, 1) }}</span>
                  </div>
                </a>
              @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $books->links() }}
            </div>
          </div>

          <!-- Right Sidebar - Related Genres (genre pages only) -->
          @if($genre)
            <aside class="w-[260px] flex-shrink-0 hidden lg:block">
              <div class="card p-5 sticky top-24 bg-white rounded shadow-sm border border-gray-100">
                <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Related Genres</h3>
                <div class="grid grid-cols-2 gap-x-3 gap-y-2">
                  @foreach($relatedGenres as $g)
                    <a
                      href="{{ route('genres.show', $g->name) }}"
                      class="text-sm text-[#00635d] hover:underline"
                    >
                      {{ $g->name }}
                    </a>
                  @endforeach
                </div>
              </div>
            </aside>
          @endif
        </div>
    </main>
</x-app-layout>
