<x-app-layout>
    <main class="flex-1 max-w-6xl mx-auto w-full px-6 py-8">
        <div class="flex flex-col md:flex-row gap-8">
          <!-- Left Column -->
          <aside class="w-full md:w-[220px] flex-shrink-0">
            <div class="aspect-[2/3] rounded overflow-hidden shadow-lg bg-gray-100">
                @if($book->cover_url)
                    <img src="{{ filter_var($book->cover_url, FILTER_VALIDATE_URL) ? $book->cover_url : Storage::url($book->cover_url) }}" alt="{{ $book->title }}" class="w-full h-full object-cover" />
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
                <button type="button" @click="open = !open" @click.away="open = false" 
                        class="w-full flex items-center justify-between text-sm font-semibold px-4 py-2.5 rounded transition-colors duration-150 {{ $userShelfName ? 'bg-white border-2 border-gray-500 text-gray-800 rounded-full hover:bg-gray-50 py-2' : 'bg-[#3c6138] hover:bg-[#2e4d2b] text-white' }}">
                  <div class="flex items-center gap-1.5 justify-center flex-1">
                    @if($userShelfName)
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                      <span class="font-bold">{{ $userShelfName }}</span>
                    @else
                      <span>Shelve</span>
                    @endif
                  </div>
                  @if(!$userShelfName)
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                  @endif
                </button>
                <div x-show="open" style="display: none;" class="absolute left-0 right-0 top-full mt-1 bg-white border border-[#e8e0d5] rounded shadow-lg z-10 py-1">
                  @php
                      $defaultOptions = ['Want to Read', 'Currently Reading', 'Read', 'Did Not Finish'];
                      $allOptions = array_merge($defaultOptions, $customShelves ?? []);
                  @endphp
                  @foreach($allOptions as $shelfName)
                    <button type="submit" name="shelf_name" value="{{ $shelfName }}" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-[#F4F1EA] transition-colors">
                      {{ $shelfName }}
                    </button>
                  @endforeach
                </div>
              </form>
            </div>
            
            {{-- Fetch-based interactive star rating --}}
            <div class="mt-4 text-center" id="star-rating-widget">
              <span class="text-xs text-gray-500 block mb-1">Rate this book</span>
              <div class="flex justify-center gap-1" id="star-row">
                @for($i=1; $i<=5; $i++)
                  <button
                    type="button"
                    data-stars="{{ $i }}"
                    data-book="{{ $book->id }}"
                    onclick="submitRating({{ $i }}, {{ $book->id }})"
                    class="star-btn p-0.5 transition-transform hover:scale-125 {{ ($userRating && $i <= $userRating) ? 'text-amber-400' : 'text-gray-300' }} hover:text-amber-400"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="{{ ($userRating && $i <= $userRating) ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="star-svg">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                  </button>
                @endfor
              </div>
              @if($userRating)
                <p class="text-xs text-gray-400 mt-1" id="my-rating-label">Your rating: {{ $userRating }}★</p>
              @else
                <p class="text-xs text-gray-400 mt-1" id="my-rating-label">Click to rate</p>
              @endif
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
                      @if($rBook->cover_url)
                          <img src="{{ filter_var($rBook->cover_url, FILTER_VALIDATE_URL) ? $rBook->cover_url : Storage::url($rBook->cover_url) }}" alt="{{ $rBook->title }}" class="w-full h-full object-cover" />
                      @else
                          <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs text-center p-2">{{ $rBook->title }}</div>
                      @endif
                    </div>
                    <h4 class="mt-2 font-bold text-xs text-gray-800 leading-snug line-clamp-2 group-hover:text-[#00635d]">
                      {{ $rBook->title }}
                    </h4>
                    <p class="text-[10px] text-gray-500 mt-0.5">
                      {{ $rBook->authors->pluck('name')->join(', ') }}
                    </p>
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
                <div class="text-5xl font-bold text-gray-800 mb-1" id="avg-rating-display">{{ number_format($book->avg_rating, 1) }}</div>
                <div class="flex mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="{{ $i <= round($book->avg_rating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= round($book->avg_rating) ? 'text-amber-400' : 'text-gray-300 fill-gray-200' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    @endfor
                </div>
                <p class="text-sm text-gray-500" id="ratings-count-display">
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

              {{-- Review Composer --}}
              @auth
              @php
                $myReview = $reviews->firstWhere('user_id', auth()->id())
                    ?? \App\Models\Review::where('user_id', auth()->id())->where('book_id', $book->id)->first();
              @endphp
              <div class="border-t border-gray-100 pt-6">
                <h4 class="text-sm font-bold text-gray-700 mb-3">{{ $myReview ? 'Edit Your Review' : 'Write a Review' }}</h4>
                @if($myReview)
                  <form action="{{ route('reviews.update', $myReview->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                @else
                  <form action="{{ route('reviews.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                @endif
                  <textarea
                    name="body"
                    rows="4"
                    placeholder="Write your thoughts about this book…"
                    class="w-full text-sm border border-gray-200 rounded px-3 py-2 text-gray-700 focus:outline-none focus:border-[#00635d] focus:ring-1 focus:ring-[#00635d] resize-none"
                    required
                  >{{ old('body', $myReview?->body) }}</textarea>
                  <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                    <input type="checkbox" name="contains_spoilers" value="1"
                           class="rounded border-gray-300 text-[#3c6138] focus:ring-[#3c6138]"
                           {{ ($myReview?->contains_spoilers) ? 'checked' : '' }}>
                    This review contains spoilers
                  </label>
                  <div class="flex gap-2">
                    <button type="submit"
                      class="bg-[#3c6138] text-white text-sm px-5 py-2 rounded font-semibold hover:bg-[#2e4d2b] transition-colors">
                      {{ $myReview ? 'Update Review' : 'Post Review' }}
                    </button>
                    @if($myReview)
                      <form action="{{ route('reviews.destroy', $myReview->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                          class="text-sm px-4 py-2 rounded border border-red-300 text-red-600 hover:bg-red-50 transition-colors"
                          onclick="return confirm('Delete your review?')">
                          Delete
                        </button>
                      </form>
                    @endif
                  </div>
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
                        <span class="font-semibold">{{ $review->user->name }}</span> rated it {{ $review->rating?->stars ?? 0 }} stars.
                        <div class="mt-1 line-clamp-2">{{ $review->body }}</div>
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
                                    $isFollowing = \DB::table('follows')->where('follower_id', auth()->id())->where('followee_id', $review->user_id)->exists();
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= ($review->rating?->stars ?? 0) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= ($review->rating?->stars ?? 0) ? 'text-amber-400' : 'text-gray-300 fill-gray-200' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @endfor
                          </div>
                          <span class="text-xs text-gray-400">·</span>
                          <span class="text-xs text-gray-400">{{ $review->created_at->format('M j, Y') }}</span>
                        </div>
                        
                        <div class="mt-2" x-data="{ spoilerRevealed: false }">
                          @if($review->contains_spoilers)
                            <div x-show="!spoilerRevealed" class="bg-gray-100 rounded border border-gray-200 p-3 text-center">
                              <p class="text-sm text-gray-600 font-semibold mb-1">This review contains spoilers.</p>
                              <button @click="spoilerRevealed = true" class="text-xs px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">Show Review</button>
                            </div>
                            <div x-show="spoilerRevealed" style="display: none;">
                                <div class="text-sm text-gray-700 leading-relaxed" :class="expanded ? '' : 'line-clamp-4'">
                                  {{ $review->body }}
                                </div>
                                
                                @if(strlen($review->body) > 200)
                                  <button @click="expanded = !expanded" class="text-sm text-[#00635d] hover:underline mt-1 block">
                                    <span x-text="expanded ? 'Show less' : 'Show more'">Show more</span>
                                  </button>
                                @endif
                            </div>
                          @else
                            <div class="text-sm text-gray-700 leading-relaxed" :class="expanded ? '' : 'line-clamp-4'">
                              {{ $review->body }}
                            </div>
                            
                            @if(strlen($review->body) > 200)
                              <button @click="expanded = !expanded" class="text-sm text-[#00635d] hover:underline mt-1 block">
                                <span x-text="expanded ? 'Show less' : 'Show more'">Show more</span>
                              </button>
                            @endif
                          @endif
                        </div>
                        
                        @php
                            $hasLiked = auth()->check() ? \DB::table('review_likes')->where('review_id', $review->id)->where('user_id', auth()->id())->exists() : false;
                        @endphp
                        <div class="flex items-center gap-5 mt-3 border-t border-gray-50 pt-3" x-data="{ showComments: false }">
                          <button onclick="toggleLike({{ $review->id }}, this)" data-liked="{{ $hasLiked ? 'true' : 'false' }}" class="flex items-center gap-1.5 text-sm transition-colors {{ $hasLiked ? 'text-red-500' : 'text-gray-500 hover:text-gray-700' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ $hasLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="like-icon"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            <span class="like-count">{{ $review->likes_count ?? 0 }}</span>
                          </button>
                          <button @click="showComments = !showComments" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                            <span>{{ $review->comments_count ?? 0 }}</span>
                          </button>
                          @auth
                            @if(auth()->id() !== $review->user_id)
                                <form action="{{ route('reports.store') }}" method="POST" class="ml-auto inline">
                                    @csrf
                                    <input type="hidden" name="reportable_type" value="review">
                                    <input type="hidden" name="reportable_id" value="{{ $review->id }}">
                                    <input type="hidden" name="reason" value="Inappropriate content">
                                    <button type="submit" class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-red-500 transition-colors" title="Report Review" onclick="return confirm('Report this review?');">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                                    </button>
                                </form>
                            @endif
                          @endauth
                        </div>

                        {{-- Comments Section --}}
                        <div x-show="showComments" style="display: none;" class="mt-4 pl-4 border-l-2 border-gray-100 space-y-4">
                            @foreach($review->comments as $comment)
                                <div class="text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-800">{{ $comment->user->name }}</span>
                                        <div class="flex items-center gap-2 text-xs text-gray-400">
                                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                                            @if(auth()->id() === $comment->user_id)
                                                <form action="{{ route('reviews.comments.destroy', $comment->id) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                                </form>
                                            @else
                                                <form action="{{ route('reports.store') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="reportable_type" value="comment">
                                                    <input type="hidden" name="reportable_id" value="{{ $comment->id }}">
                                                    <input type="hidden" name="reason" value="Inappropriate content">
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 hover:underline" title="Report Comment" onclick="return confirm('Report this comment?');">Report</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-gray-700 mt-1">{{ $comment->body }}</div>
                                </div>
                            @endforeach

                            @auth
                            <form action="{{ route('reviews.comments.store', $review->id) }}" method="POST" class="mt-3 flex gap-2">
                                @csrf
                                <input type="text" name="body" placeholder="Write a comment..." class="flex-1 text-sm border border-gray-200 rounded px-3 py-1.5 focus:outline-none focus:border-[#00635d] focus:ring-1 focus:ring-[#00635d]" required>
                                <button type="submit" class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded text-sm font-semibold hover:bg-gray-200 transition-colors">Post</button>
                            </form>
                            @else
                            <div class="text-sm text-gray-500 mt-2">
                                <a href="{{ route('login') }}" class="text-[#00635d] hover:underline">Log in</a> to post a comment.
                            </div>
                            @endauth
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

    <script>
      // Fetch-based star rating — no page reload
      function submitRating(stars, bookId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Optimistic UI — paint stars immediately
        paintStars(stars);

        fetch('{{ route("ratings.store") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
          body: JSON.stringify({ book_id: bookId, stars: stars }),
        })
        .then(r => r.json())
        .then(data => {
          // Update average display
          const avgEl = document.getElementById('avg-rating-display');
          if (avgEl) avgEl.textContent = parseFloat(data.avg_rating).toFixed(1);
          const cntEl = document.getElementById('ratings-count-display');
          if (cntEl) cntEl.textContent = data.ratings_count + ' ratings';
          const lbl = document.getElementById('my-rating-label');
          if (lbl) lbl.textContent = 'Your rating: ' + data.user_rating + '★';
        })
        .catch(() => {
          // On error just reload to reflect real state
          window.location.reload();
        });
      }

      // Paint stars filled/empty up to the given value
      function paintStars(value) {
        document.querySelectorAll('.star-btn').forEach(btn => {
          const n = parseInt(btn.dataset.stars);
          const svg = btn.querySelector('.star-svg');
          if (n <= value) {
            btn.classList.add('text-amber-400');
            btn.classList.remove('text-gray-300');
            svg.setAttribute('fill', 'currentColor');
          } else {
            btn.classList.add('text-gray-300');
            btn.classList.remove('text-amber-400');
            svg.setAttribute('fill', 'none');
          }
        });
      }

      // Hover preview
      document.querySelectorAll('.star-btn').forEach(btn => {
        btn.addEventListener('mouseenter', () => paintStars(parseInt(btn.dataset.stars)));
        btn.addEventListener('mouseleave', () => {
          // Restore saved rating (read from label)
          const lbl = document.getElementById('my-rating-label');
          const match = lbl ? lbl.textContent.match(/(\d)/) : null;
          paintStars(match ? parseInt(match[1]) : 0);
        });
      });

      // Toggle Review Like
      function toggleLike(reviewId, btnEl) {
        @auth
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const isLiked = btnEl.dataset.liked === 'true';
        
        // Optimistic update
        btnEl.dataset.liked = !isLiked;
        const icon = btnEl.querySelector('.like-icon');
        const countSpan = btnEl.querySelector('.like-count');
        let currentCount = parseInt(countSpan.textContent) || 0;
        
        if (!isLiked) {
            btnEl.classList.remove('text-gray-500', 'hover:text-gray-700');
            btnEl.classList.add('text-red-500');
            icon.setAttribute('fill', 'currentColor');
            countSpan.textContent = currentCount + 1;
        } else {
            btnEl.classList.add('text-gray-500', 'hover:text-gray-700');
            btnEl.classList.remove('text-red-500');
            icon.setAttribute('fill', 'none');
            countSpan.textContent = currentCount - 1;
        }

        fetch(`/reviews/${reviewId}/toggle-like`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          }
        })
        .then(r => r.json())
        .then(data => {
            // Ensure synced with server
            countSpan.textContent = data.likes_count;
            btnEl.dataset.liked = data.status === 'liked' ? 'true' : 'false';
            if (data.status === 'liked') {
                btnEl.classList.remove('text-gray-500', 'hover:text-gray-700');
                btnEl.classList.add('text-red-500');
                icon.setAttribute('fill', 'currentColor');
            } else {
                btnEl.classList.add('text-gray-500', 'hover:text-gray-700');
                btnEl.classList.remove('text-red-500');
                icon.setAttribute('fill', 'none');
            }
        })
        .catch(() => {
          // On error just reload to reflect real state
          window.location.reload();
        });
        @else
        window.location.href = "{{ route('login') }}";
        @endauth
      }
    </script>
</x-app-layout>
