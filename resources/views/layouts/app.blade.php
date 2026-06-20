<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BookShelf') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js for dropdowns -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased text-[#333] bg-[#F4F1EA]">
        <div class="min-h-screen flex flex-col">
            <!-- Navbar -->
            <nav class="bg-white border-b border-[#DDD8CC] sticky top-0 z-50" x-data="{ openDropdown: null }">
              <div class="max-w-[1200px] mx-auto px-6 h-14 flex items-center gap-6">
                <!-- Wordmark -->
                <a
                  href="/"
                  class="text-[#382110] text-2xl font-serif font-bold tracking-tight shrink-0"
                >
                  bookshelf
                </a>

                <!-- Nav links -->
                <ul class="flex items-center gap-1 shrink-0">
                    <li class="relative">
                      <a
                        href="/"
                        class="flex items-center gap-0.5 px-3 py-1.5 text-sm rounded transition-colors {{ request()->is('/') ? 'text-[#00635D] font-semibold' : 'text-[#333] hover:text-[#00635D] hover:bg-[#F4F1EA]' }}"
                      >
                        Home
                      </a>
                    </li>
                    <li class="relative">
                      <a
                        href="/shelves"
                        class="flex items-center gap-0.5 px-3 py-1.5 text-sm rounded transition-colors {{ request()->is('shelves*') ? 'text-[#00635D] font-semibold' : 'text-[#333] hover:text-[#00635D] hover:bg-[#F4F1EA]' }}"
                      >
                        My Shelves
                      </a>
                    </li>
                    <li class="relative">
                      <button
                        class="flex items-center gap-0.5 px-3 py-1.5 text-sm rounded transition-colors text-[#333] hover:text-[#00635D] hover:bg-[#F4F1EA]"
                        @click="openDropdown = openDropdown === 'Browse' ? null : 'Browse'"
                      >
                        Browse
                        <svg class="w-3 h-3 mt-px opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                      </button>
                      <div x-show="openDropdown === 'Browse'" @click.away="openDropdown = null" style="display: none;" class="absolute top-full left-0 mt-1 bg-white border border-[#DDD8CC] rounded shadow-lg py-1 min-w-[170px] z-50">
                        @foreach(['Lists', 'News & Interviews', 'New Releases', 'Popular', 'Choice Awards', 'Genres'] as $item)
                            <a href="#" class="block px-4 py-2 text-sm text-[#333] hover:bg-[#F4F1EA] hover:text-[#00635D] transition-colors">
                              {{ $item }}
                            </a>
                        @endforeach
                      </div>
                    </li>
                    <li class="relative">
                      <button
                        class="flex items-center gap-0.5 px-3 py-1.5 text-sm rounded transition-colors text-[#333] hover:text-[#00635D] hover:bg-[#F4F1EA]"
                        @click="openDropdown = openDropdown === 'Community' ? null : 'Community'"
                      >
                        Community
                        <svg class="w-3 h-3 mt-px opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                      </button>
                      <div x-show="openDropdown === 'Community'" @click.away="openDropdown = null" style="display: none;" class="absolute top-full left-0 mt-1 bg-white border border-[#DDD8CC] rounded shadow-lg py-1 min-w-[170px] z-50">
                        @foreach(['Groups', 'Discussions', 'Friends', 'Recommendations', 'Quotes'] as $item)
                            <a href="#" class="block px-4 py-2 text-sm text-[#333] hover:bg-[#F4F1EA] hover:text-[#00635D] transition-colors">
                              {{ $item }}
                            </a>
                        @endforeach
                      </div>
                    </li>
                </ul>

                <!-- Search -->
                <div class="flex-1 flex justify-center px-4">
                  <form action="{{ route('books.index') }}" method="GET" class="relative w-full max-w-sm">
                    <input
                      type="text"
                      name="q"
                      placeholder="Search books"
                      class="w-full pl-4 pr-10 py-1.5 text-sm border border-[#C8C0B0] rounded-full bg-white text-[#333] placeholder-[#999] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition"
                    />
                    <button type="submit" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#888]">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                  </form>
                </div>

                <!-- Right icons -->
                <div class="flex items-center gap-3 shrink-0">
                  @auth
                      <button class="p-1.5 rounded-full hover:bg-[#F4F1EA] transition-colors text-[#555] hover:text-[#00635D]">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                      </button>
                      <div class="relative" x-data="{ open: false }">
                          <button
                            @click="open = !open"
                            class="w-8 h-8 rounded-full overflow-hidden border-2 transition-colors shrink-0 {{ request()->is('profile*') ? 'border-[#00635D]' : 'border-[#C8C0B0] hover:border-[#00635D]' }}"
                          >
                            @if(auth()->user()->avatar_url)
                                <img
                                  src="{{ Storage::url(auth()->user()->avatar_url) }}"
                                  alt="Profile"
                                  class="w-full h-full object-cover"
                                />
                            @else
                                <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs font-bold">
                                    {{ substr(auth()->user()->username, 0, 1) }}
                                </div>
                            @endif
                          </button>
                          
                          <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-48 bg-white border border-[#DDD8CC] rounded shadow-lg py-1 z-50">
                              <a href="{{ route('profile.show', auth()->user()->username) }}" class="block px-4 py-2 text-sm text-[#333] hover:bg-[#F4F1EA] hover:text-[#00635D] transition-colors">
                                  Profile
                              </a>
                              <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-[#333] hover:bg-[#F4F1EA] hover:text-[#00635D] transition-colors">
                                  Account Settings
                              </a>
                              <form method="POST" action="{{ route('logout') }}">
                                  @csrf
                                  <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-[#333] hover:bg-[#F4F1EA] hover:text-[#00635D] transition-colors">
                                      Sign out
                                  </button>
                              </form>
                          </div>
                      </div>
                  @else
                      <a href="{{ route('login') }}" class="px-4 py-1.5 text-sm rounded bg-black text-white hover:bg-gray-800 transition-colors">
                          Sign in
                      </a>
                  @endauth
                </div>
              </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-grow">
                @yield('content')
                
                @if(isset($slot))
                    {{ $slot }}
                @endif
            </main>

            <!-- Footer -->
            <footer class="mt-10 py-8 text-center bg-[#F4F1EA]">
              <div class="text-sm text-[#888] flex items-center justify-center gap-2 flex-wrap">
                <span class="font-serif font-bold text-[#382110] text-base">bookshelf</span>
                <span class="text-[#C8C0B0]">·</span>
                <a href="#" class="hover:text-[#00635D] hover:underline transition-colors">About</a>
                <span class="text-[#C8C0B0]">·</span>
                <a href="#" class="hover:text-[#00635D] hover:underline transition-colors">Help</a>
                <span class="text-[#C8C0B0]">·</span>
                <a href="#" class="hover:text-[#00635D] hover:underline transition-colors">Privacy</a>
              </div>
              <p class="text-xs text-[#AAA] mt-2">© {{ date('Y') }} BookShelf</p>
            </footer>
        </div>
    </body>
</html>
