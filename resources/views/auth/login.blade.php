<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Wordmark -->
    <div class="text-center mb-8">
      <span
        class="text-[2.6rem] tracking-tight text-black"
        style="font-family: 'Playfair Display', Georgia, serif; font-weight: 400"
      >
        bookshelf
      </span>
    </div>

    <!-- Heading -->
    <h1
      class="text-[2rem] font-bold text-black mb-6 leading-tight"
      style="font-family: 'Playfair Display', Georgia, serif"
    >
      Sign in
    </h1>

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
        @csrf

        <!-- Email -->
        <div class="flex flex-col gap-1.5">
            <label for="email" class="text-sm text-black" style="font-family: 'Inter', sans-serif">
                Email
            </label>
            <input
              id="email"
              type="email"
              name="email"
              value="{{ old('email') }}"
              required autofocus autocomplete="username"
              class="w-full bg-gray-100 border-0 rounded-full px-5 py-3 text-sm text-black placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition"
              style="font-family: 'Inter', sans-serif"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="text-sm text-black" style="font-family: 'Inter', sans-serif">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a
                      href="{{ route('password.request') }}"
                      class="text-sm text-gray-500 underline hover:text-black transition-colors"
                      style="font-family: 'Inter', sans-serif"
                    >
                        Password assistance
                    </a>
                @endif
            </div>
            <input
              id="password"
              type="password"
              name="password"
              required autocomplete="current-password"
              class="w-full bg-gray-100 border-0 rounded-full px-5 py-3 text-sm text-black placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition"
              style="font-family: 'Inter', sans-serif"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Submit -->
        <button
          type="submit"
          class="w-full bg-black text-white font-bold text-sm rounded-full py-3.5 mt-1 hover:bg-gray-900 active:bg-gray-800 transition-colors"
          style="font-family: 'Inter', sans-serif"
        >
          Sign in
        </button>
        
        <!-- Keep me signed in -->
        <div class="flex items-center gap-2 mt-2" style="font-family: 'Inter', sans-serif">
          <input
            id="remember_me"
            name="remember"
            type="checkbox"
            class="w-4 h-4 accent-black cursor-pointer"
          />
          <label for="remember_me" class="text-sm text-gray-600 cursor-pointer select-none">
            Keep me signed in.
          </label>
          <a href="#" class="text-sm text-gray-500 underline hover:text-black transition-colors">
            Details
          </a>
        </div>
    </form>

    <!-- Terms -->
    <p
      class="text-xs text-gray-400 text-center mt-5 leading-relaxed"
      style="font-family: 'Inter', sans-serif"
    >
      By signing in, you agree to the BookShelf{' '}
      <a href="#" class="underline hover:text-gray-600 transition-colors">
        Terms of Service
      </a>{' '}
      and{' '}
      <a href="#" class="underline hover:text-gray-600 transition-colors">
        Privacy Policy
      </a>
    </p>

    <!-- Divider -->
    <div class="flex items-center gap-3 my-7">
      <div class="flex-1 h-px bg-gray-200"></div>
      <span
        class="text-sm text-gray-400 whitespace-nowrap"
        style="font-family: 'Inter', sans-serif"
      >
        New to BookShelf?
      </span>
      <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <!-- Sign up outlined button -->
    <a
      href="{{ route('register') }}"
      class="block text-center w-full border border-gray-300 text-black font-semibold text-sm rounded-full py-3.5 hover:border-gray-500 hover:bg-gray-50 active:bg-gray-100 transition-colors"
      style="font-family: 'Inter', sans-serif"
    >
      Sign up
    </a>

    <!-- Footer -->
    <div class="mt-10 text-center" style="font-family: 'Inter', sans-serif">
      <p class="text-xs text-gray-400 space-x-2">
        <a href="#" class="underline hover:text-gray-600 transition-colors">
          Terms of Service
        </a>
        <span>·</span>
        <a href="#" class="underline hover:text-gray-600 transition-colors">
          Privacy
        </a>
        <span>·</span>
        <a href="#" class="underline hover:text-gray-600 transition-colors">
          Help
        </a>
      </p>
      <p class="text-xs text-gray-400 mt-1">&copy; {{ date('Y') }} BookShelf</p>
    </div>
</x-guest-layout>
