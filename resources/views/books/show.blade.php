<x-app-layout>
    <main class="flex-1 max-w-6xl mx-auto w-full px-6 py-8">
        <div class="flex flex-col md:flex-row gap-8">
          <!-- Left Column -->
          <aside class="w-full md:w-[220px] flex-shrink-0">
            <div class="aspect-[2/3] rounded overflow-hidden shadow-lg bg-gray-100">
                @if($book->cover_image)
                    <img src="{{ filter_var($book->cover_image, FILTER_VALIDATE_URL) ? $book->cover_image : Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover" />
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">No Cover</div>
                @endif
            </div>
            
            @auth
            <div class="mt-4">
              <!-- Shelf Button -->
              <form action="{{ route('shelves.store') }}" method="POST" class="relative group" x-data="{ open: false }">
                @csrf
                <input type="hidden" name="book_id" value="{{ $book->id }}">
                <button type="button" @click="open = !open" @click.away="open = false" class="w-full flex items-center justify-between bg-[#3c6138] hover:bg-[#2e4d2b] text-white text-sm font-semibold px-4 py-2.5 rounded transition-colors duration-150">
                  <span>Shelve</span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div x-show="open" style="display: none;" class="absolute left-0 right-0 top-full mt-1 bg-white border border-[#e8e0d5] rounded shadow-lg z-10 py-1">
                  @foreach(['Want to Read', 'Currently Reading', 'Read'] as $shelfName)
                    <button type="submit" name="shelf_name" value="{{ $shelfName }}" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-[#F4F1EA] transition-colors">
                      {{ $shelfName }}
                    </button>
                  @endforeach
                </div>
              </form>
            </div>
            
            <div class="mt-4 text-center">
              <span class="text-xs text-gray-500 block mb-1">Rate this book</span>
              <form action="{{ route('ratings.store') }}" method="POST" class="flex justify-center gap-1">
                @csrf
                <input type="hidden" name="book_id" value="{{ $book->id }}">
                @for($i=1; $i<=5; $i++)
                  <button type="submit" name="stars" value="{{ $i }}" class="p-0.5 transition-transform hover:scale-110 text-gray-300 hover:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  </button>
                @endfor
              </form>
            </div>
            @else
            <div class="mt-4 text-center text-sm text-gray-500">
                <a href="{{ route('login') }}" class="text-[#00635d] hover:underline">Log in</a> to shelve or rate this book.
            </div>
            @endauth
          </aside>

          <!-- Main Column -->
          <div class="flex-1 min-w-0" x-data="{ descExpanded: false, authorBioExpanded: false }">
            <!-- Book Header -->
            <h1 class="font-serif text-3xl font-bold text-gray-800 mb-2">{{ $book->title }}</h1>
            <p class="text-sm text-gray-500 mb-2">
              by 
              @foreach($book->authors as $author)
                <a href="{{ route('books.index', ['author_id' => $author->id]) }}" class="font-semibold text-[#00635d] hover:underline">{{ $author->name }}</a>{{ !$loop->last ? ', ' : '' }}
              @endforeach
            </p>
            
            <div class="flex items-center gap-1 mb-4">
              <div class="flex">
                @for($i = 1; $i <= 5; $i++)
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="{{ $i <= round($book->avg_rating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= round($book->avg_rating) ? 'text-amber-400' : 'text-gray-300 fill-gray-200' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                @endfor
              </div>
              <span class="text-sm font-semibold text-gray-700 ml-1">{{ number_format($book->avg_rating, 1) }}</span>
              <span class="text-xs text-gray-500 ml-1">
                {{ number_format($book->ratings_count) }} ratings
              </span>
            </div>

            <!-- Description -->
            <div class="text-gray-700 leading-relaxed mb-2" :class="descExpanded ? '' : 'line-clamp-3'">
              {!! nl2br(e($book->description)) !!}
            </div>
            <button @click="descExpanded = !descExpanded" class="text-sm text-[#00635d] hover:underline mb-4">
              <span x-text="descExpanded ? 'Show less' : 'Show more'">Show more</span>
            </button>

            <!-- Genre Tags -->
            <div class="flex flex-wrap gap-2 mb-4">
              @foreach($book->genres as $g)
                <a href="{{ route('genres.show', $g->name) }}" class="px-3 py-1 text-xs font-semibold border border-[#3c6138] text-[#3c6138] rounded-full hover:bg-[#3c6138] hover:text-white transition-colors">
                  {{ $g->name }}
                </a>
              @endforeach
            </div>

            <!-- Quick Facts -->
            <p class="text-sm text-gray-500 mb-2">
              {{ $book->page_count ?? 'Unknown' }} pages · First published {{ $book->published_date ? date('Y', strtotime($book->published_date)) : 'Unknown' }}
            </p>

            <!-- Social Proof -->
            <p class="text-sm text-gray-500 mb-8">
              {{ number_format($currentlyReadingCount) }} people currently reading · {{ number_format($wantToReadCount) }} want to read
            </p>

            <!-- About the Author -->
            @if($book->authors->count() > 0)
            <div class="card p-6 mb-8 bg-white border border-gray-100 rounded shadow-sm">
              <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">About the Author</h3>
              <div class="flex gap-4">
                <div class="w-16 h-16 rounded-full bg-gray-200 flex-shrink-0 flex items-center justify-center text-gray-500 text-xl font-bold uppercase">
                    {{ substr($book->authors->first()->name, 0, 1) }}
                </div>
                <div>
                  <h4 class="font-bold text-gray-800 mb-1">{{ $book->authors->first()->name }}</h4>
                  <div class="text-sm text-gray-600 leading-relaxed" :class="authorBioExpanded ? '' : 'line-clamp-2'">
                    {{ $book->authors->first()->bio ?? 'No bio available.' }}
                  </div>
                  <div class="flex items-center gap-4 mt-2">
                    <button @click="authorBioExpanded = !authorBioExpanded" class="text-sm text-[#00635d] hover:underline">
                      <span x-text="authorBioExpanded ? 'Show less' : 'Show more'">Show more</span>
                    </button>
                    <a href="{{ route('books.index', ['author_id' => $book->authors->first()->id]) }}" class="text-sm text-[#00635d] hover:underline">
                      See all books by {{ $book->authors->first()->name }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
            @endif

            <!-- Readers Also Enjoyed -->
            @if($relatedBooks->count() > 0)
            <section class="mb-8">
              <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Readers Also Enjoyed</h3>
              <div class="flex gap-4 overflow-x-auto pb-4 custom-scrollbar">
                @foreach($relatedBooks as $rBook)
                  <a href="{{ route('books.show', $rBook) }}" class="flex-shrink-0 w-[140px] group block">
                    <div class="aspect-[2/3] rounded overflow-hidden shadow-md transition-all duration-200 group-hover:shadow-xl group-hover:-translate-y-1 bg-gray-100">
                      @if($rBook->cover_image)
                          <img src="{{ filter_var($rBook->cover_image, FILTER_VALIDATE_URL) ? $rBook->cover_image : Storage::url($rBook->cover_image) }}" alt="{{ $rBook->title }}" class="w-full h-full object-cover" />
                      @else
                          <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs text-center p-2">{{ $rBook->title }}</div>
                      @endif
                    </div>
                    <h4 class="mt-2 font-bold text-xs text-gray-800 leading-snug line-clamp-2 group-hover:text-[#00635d]">
                      {{ $rBook->title }}
                    </h4>
                    <div class="flex items-center gap-1 mt-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                      <span class="text-xs text-gray-600 font-semibold">{{ number_format($rBook->avg_rating, 1) }}</span>
                    </div>
                  </a>
                @endforeach
              </div>
            </section>
            @endif

            <!-- Ratings & Reviews Summary -->
            <section class="mb-8">
              <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Ratings & Reviews</h3>
              <div class="flex flex-col items-center mb-6">
                <div class="text-5xl font-bold text-gray-800 mb-1">{{ number_format($book->avg_rating, 1) }}</div>
                <div class="flex mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="{{ $i <= round($book->avg_rating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= round($book->avg_rating) ? 'text-amber-400' : 'text-gray-300 fill-gray-200' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    @endfor
                </div>
                <p class="text-sm text-gray-500">
                  {{ number_format($book->ratings_count) }} ratings
                </p>
              </div>

              <!-- Breakdown -->
              <div class="max-w-md mx-auto space-y-1.5 mb-8">
                @foreach($starBreakdown as $stars => $count)
                  @php $pct = $book->ratings_count > 0 ? round(($count / $book->ratings_count) * 100) : 0; @endphp
                  <div class="flex items-center gap-2 text-sm">
                    <span class="w-8 text-gray-600">{{ $stars }} star</span>
                    <div class="flex-1 h-5 bg-gray-100 rounded overflow-hidden">
                      <div class="h-full bg-amber-400 rounded" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="w-16 text-right text-gray-500">{{ number_format($count) }}</span>
                    <span class="w-10 text-right text-gray-400">{{ $pct }}%</span>
                  </div>
                @endforeach
              </div>

              <!-- Write a Review -->
              @auth
              <div class="flex flex-col items-center border-t border-gray-100 pt-6">
                <form action="{{ route('reviews.create') }}" method="GET">
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <button type="submit" class="bg-[#3c6138] text-white px-8 py-2 rounded font-semibold hover:bg-[#2e4d2b] transition-colors">Write a Review</button>
                </form>
              </div>
              @endauth
            </section>

            <!-- Friends & Following -->
            @auth
            <section class="mb-8">
              <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider mb-3 border-b border-gray-100 pb-2">Friends & Following</h3>
              @if($followingReviews->count() > 0)
                <div class="space-y-4">
                  @foreach($followingReviews as $review)
                    <div class="text-sm text-gray-700 border-l-2 border-[#3c6138] pl-3 py-1 bg-gray-50">
                        <span class="font-semibold">{{ $review->user->name }}</span> rated it {{ $review->rating }} stars.
                        <div class="mt-1 line-clamp-2">{{ $review->content }}</div>
                    </div>
                  @endforeach
                </div>
              @else
                <p class="text-sm text-gray-400">No one you follow has reviewed this yet.</p>
              @endif
            </section>
            @endauth

            <!-- Community Reviews -->
            <section>
              <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Community Reviews</h3>
              <div class="space-y-8">
                @foreach($reviews as $review)
                    <div class="flex gap-4" x-data="{ expanded: false }">
                      <div class="w-12 h-12 rounded-full bg-gray-200 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold uppercase overflow-hidden">
                        @if($review->user->avatar)
                            <img src="{{ filter_var($review->user->avatar, FILTER_VALIDATE_URL) ? $review->user->avatar : Storage::url($review->user->avatar) }}" alt="{{ $review->user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($review->user->name, 0, 1) }}
                        @endif
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                          <div>
                            <span class="font-bold text-sm text-gray-800">{{ $review->user->name }}</span>
                          </div>
                          @auth
                            @if(auth()->id() !== $review->user_id)
                                @php
                                    $isFollowing = \DB::table('follows')->where('follower_id', auth()->id())->where('following_id', $review->user_id)->exists();
                                @endphp
                                @if($isFollowing)
                                    <form action="{{ route('users.unfollow', $review->user_id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold px-3 py-1 rounded transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200">Following</button>
                                    </form>
                                @else
                                    <form action="{{ route('users.follow', $review->user_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold px-3 py-1 rounded transition-colors bg-[#3c6138] text-white hover:bg-[#2e4d2b]">Follow</button>
                                    </form>
                                @endif
                            @endif
                          @endauth
                        </div>
                        
                        <div class="flex items-center gap-2 mt-1.5">
                          <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-300 fill-gray-200' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @endfor
                          </div>
                          <span class="text-xs text-gray-400">·</span>
                          <span class="text-xs text-gray-400">{{ $review->created_at->format('M j, Y') }}</span>
                        </div>
                        
                        <div class="text-sm text-gray-700 mt-2 leading-relaxed" :class="expanded ? '' : 'line-clamp-4'">
                          {{ $review->content }}
                        </div>
                        
                        @if(strlen($review->content) > 200)
                          <button @click="expanded = !expanded" class="text-sm text-[#00635d] hover:underline mt-1">
                            <span x-text="expanded ? 'Show less' : 'Show more'">Show more</span>
                          </button>
                        @endif
                        
                        <div class="flex items-center gap-5 mt-3 border-t border-gray-50 pt-3">
                          <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            <span>{{ $review->likes_count ?? 0 }}</span>
                          </div>
                          <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                            <span>{{ $review->comments_count ?? 0 }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                @endforeach
                
                @if($reviews->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">No reviews yet. Be the first!</p>
                @endif
              </div>
              
              <div class="mt-8">
                  {{ $reviews->links() }}
              </div>
            </section>
          </div>
        </div>
    </main>
    
    <style>
      .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
      }
      .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
      }
      .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d4d4d4; 
        border-radius: 4px;
      }
      .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a3a3a3; 
      }
    </style>
</x-app-layout>
