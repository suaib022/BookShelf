<x-guest-layout>
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
      Create Account
    </h1>

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-5">
        @csrf

        <!-- Your name -->
        <div class="flex flex-col gap-1.5">
            <label for="name" class="text-sm text-black" style="font-family: 'Inter', sans-serif">
                Your name
            </label>
            <input
              id="name"
              type="text"
              name="name"
              value="{{ old('name') }}"
              required autofocus autocomplete="name"
              placeholder="First and last name"
              class="w-full bg-gray-100 border-0 rounded-full px-5 py-3 text-sm text-black placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition"
              style="font-family: 'Inter', sans-serif"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="flex flex-col gap-1.5">
            <label for="username" class="text-sm text-black" style="font-family: 'Inter', sans-serif">
                Username
            </label>
            <input
              id="username"
              type="text"
              name="username"
              value="{{ old('username') }}"
              required autocomplete="username"
              placeholder="Pick a username"
              class="w-full bg-gray-100 border-0 rounded-full px-5 py-3 text-sm text-black placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition"
              style="font-family: 'Inter', sans-serif"
            />
            <p class="text-xs text-gray-400 px-1" style="font-family: 'Inter', sans-serif">
                This will be your profile URL — letters, numbers, and underscores only
            </p>
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

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
              required autocomplete="email"
              placeholder=""
              class="w-full bg-gray-100 border-0 rounded-full px-5 py-3 text-sm text-black placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition"
              style="font-family: 'Inter', sans-serif"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-1.5" x-data="{ show: false }">
            <label for="password" class="text-sm text-black" style="font-family: 'Inter', sans-serif">
                Password
            </label>
            <div class="relative w-full">
                <input
                  id="password"
                  :type="show ? 'text' : 'password'"
                  name="password"
                  required autocomplete="new-password"
                  placeholder="At least 8 characters"
                  class="w-full bg-gray-100 border-0 rounded-full px-5 py-3 pr-12 text-sm text-black placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition"
                  style="font-family: 'Inter', sans-serif"
                />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-gray-500 hover:text-gray-700">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" style="display:none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            <p class="text-xs text-gray-400 px-1" style="font-family: 'Inter', sans-serif">
                Passwords must be at least 8 characters.
            </p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Re-enter password -->
        <div class="flex flex-col gap-1.5" x-data="{ show: false }">
            <label for="password_confirmation" class="text-sm text-black" style="font-family: 'Inter', sans-serif">
                Re-enter password
            </label>
            <div class="relative w-full">
                <input
                  id="password_confirmation"
                  :type="show ? 'text' : 'password'"
                  name="password_confirmation"
                  required autocomplete="new-password"
                  placeholder=""
                  class="w-full bg-gray-100 border-0 rounded-full px-5 py-3 pr-12 text-sm text-black placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition"
                  style="font-family: 'Inter', sans-serif"
                />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-gray-500 hover:text-gray-700">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" style="display:none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <button
          type="submit"
          class="w-full bg-black text-white font-bold text-sm rounded-full py-3.5 mt-1 hover:bg-gray-900 active:bg-gray-800 transition-colors"
          style="font-family: 'Inter', sans-serif"
        >
          Create account
        </button>
    </form>

    <!-- Terms -->
    <p
      class="text-xs text-gray-400 text-center mt-5 leading-relaxed"
      style="font-family: 'Inter', sans-serif"
    >
      By creating an account, you agree to the BookShelf
      <a href="#" class="underline hover:text-gray-600 transition-colors">
        Terms of Service
      </a>
      and
      <a href="#" class="underline hover:text-gray-600 transition-colors">
        Privacy Policy
      </a>
    </p>

    <!-- Sign in link -->
    <p
      class="text-sm text-gray-500 text-center mt-8"
      style="font-family: 'Inter', sans-serif"
    >
      Already have an account?
      <a
        href="{{ route('login') }}"
        class="underline text-gray-500 hover:text-black transition-colors cursor-pointer"
      >
        Sign in
      </a>
    </p>
</x-guest-layout>
