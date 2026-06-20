@extends('layouts.app')

@section('content')
<div class="max-w-[860px] mx-auto px-6 py-8" x-data="{ activeTab: 'profile', showDelete: false }">
    <!-- Page heading row -->
    <div class="flex items-baseline justify-between mb-6">
        <h1 class="text-2xl font-bold text-[#382110]">Account Settings</h1>
        <a href="{{ route('profile.show', $user->username ?? 'user') }}" class="text-sm text-[#00635D] hover:underline font-medium transition-colors">
            View My Profile
        </a>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded border border-green-200 text-sm">
            Profile updated successfully.
        </div>
    @endif
    
    @if (session('status') === 'password-updated')
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded border border-green-200 text-sm">
            Password updated successfully.
        </div>
    @endif

    <!-- Tab row -->
    <div class="flex gap-0 border-b border-[#DDD8CC] mb-7">
        <button @click="activeTab = 'profile'" :class="{'border-[#382110] text-[#382110]': activeTab === 'profile', 'border-transparent text-[#777] hover:text-[#382110]': activeTab !== 'profile'}" class="px-5 py-2.5 text-sm font-semibold capitalize transition-colors border-b-2 -mb-px">
            Profile
        </button>
        <button @click="activeTab = 'account'" :class="{'border-[#382110] text-[#382110]': activeTab === 'account', 'border-transparent text-[#777] hover:text-[#382110]': activeTab !== 'account'}" class="px-5 py-2.5 text-sm font-semibold capitalize transition-colors border-b-2 -mb-px">
            Account
        </button>
    </div>

    <!-- Tab content -->
    <div class="bg-white border border-[#DDD8CC] rounded-md p-6 sm:p-8">
        
        <!-- Profile Tab -->
        <div x-show="activeTab === 'profile'" style="display: none;" x-transition>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')
                
                <div class="flex flex-col-reverse sm:flex-row gap-8 items-start">
                    <!-- Left: form fields -->
                    <div class="flex-1 min-w-0 space-y-5">
                        
                        <!-- Your Name -->
                        <div>
                            <label for="name" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                                Your Name
                                <span class="text-red-500">*</span>
                            </label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                                Username
                                <span class="text-red-500">*</span>
                            </label>
                            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                            <p class="text-xs text-[#888] mt-1">
                                Your profile URL: <span class="text-[#555] font-medium">{{ config('app.url') }}/users/{{ $user->username ?? '…' }}</span>
                            </p>
                            <x-input-error class="mt-2" :messages="$errors->get('username')" />
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                                Location
                            </label>
                            <input id="location" name="location" type="text" value="{{ old('location', $user->location) }}" placeholder="City, Country" class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                                Website <span class="normal-case tracking-normal font-normal text-[#888]">(Optional)</span>
                            </label>
                            <input id="website" name="website" type="url" value="{{ old('website', $user->website) }}" placeholder="https://yoursite.com" class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                            <x-input-error class="mt-2" :messages="$errors->get('website')" />
                        </div>

                        <!-- About Me -->
                        <div>
                            <label for="bio" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                                About Me
                            </label>
                            <textarea id="bio" name="bio" rows="5" class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition resize-y">{{ old('bio', $user->bio) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                        </div>

                        <!-- Favorite Genres -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                                Favorite Genres
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($genres as $genre)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}" {{ in_array($genre->id, old('genres', $userGenres)) ? 'checked' : '' }} class="peer hidden" />
                                        <div class="px-3 py-1.5 rounded-full border border-[#C8C0B0] text-xs font-semibold text-[#555] transition-colors peer-checked:bg-[#00635D] peer-checked:border-[#00635D] peer-checked:text-white hover:border-[#00635D]">
                                            {{ $genre->name }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-[#DDD8CC]" />

                        <!-- Private Profile -->
                        <div class="flex items-start gap-4">
                            <label class="relative inline-flex items-center cursor-pointer mt-0.5 shrink-0">
                                <input type="hidden" name="is_private" value="0">
                                <input type="checkbox" name="is_private" value="1" class="sr-only peer" {{ old('is_private', $user->is_private) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-[#C8C0B0] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#00635D]"></div>
                            </label>
                            <div>
                                <p class="text-sm font-semibold text-[#382110] leading-tight">Private Profile</p>
                                <p class="text-xs text-[#888] mt-0.5 leading-relaxed">
                                    Only people who follow you can see your shelves and activity.
                                </p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="bg-[#382110] text-white text-sm font-bold px-6 py-2.5 rounded hover:bg-[#2A180C] transition-colors">
                                Save Profile Settings
                            </button>
                        </div>
                    </div>
                    
                    <!-- Right: avatar -->
                    <div class="shrink-0 pt-1" x-data="{ photoName: null, photoPreview: null }">
                        <div class="flex flex-col items-center">
                            <div class="w-[140px] h-[140px] rounded-full overflow-hidden bg-gray-100 border border-[#DDD8CC] shadow-sm mb-4">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!photoPreview">
                                    @if($user->avatar_url)
                                        <img src="{{ Storage::url($user->avatar_url) }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                    @endif
                                </template>
                            </div>
                            
                            <input type="file" name="avatar" id="avatar" class="hidden" x-ref="photo" @change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                            ">
                            
                            <button type="button" @click="$refs.photo.click()" class="text-xs font-semibold px-4 py-1.5 border border-[#C8C0B0] text-[#555] rounded hover:bg-[#F4F1EA] transition-colors mb-2 w-full">
                                Change picture
                            </button>
                            
                            <button type="button" @click="$dispatch('open-delete-modal')" class="text-xs text-red-600 hover:underline">
                                Delete Account
                            </button>
                            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Account Tab -->
        <div x-show="activeTab === 'account'" style="display: none;" x-transition>
            
            <form method="post" action="{{ route('profile.update') }}" class="max-w-[480px] space-y-5 mb-8">
                @csrf
                @method('patch')
                
                <div>
                    <label for="email" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                        Email
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="bg-[#382110] text-white text-sm font-bold px-6 py-2.5 rounded hover:bg-[#2A180C] transition-colors">
                        Save Email
                    </button>
                </div>
            </form>

            <hr class="border-[#DDD8CC] mb-8 max-w-[480px]" />

            <form method="post" action="{{ route('password.update') }}" class="max-w-[480px] space-y-5">
                @csrf
                @method('put')
                
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#555]">
                    Change Password
                </h3>
                
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                        Current Password
                    </label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                    <x-input-error class="mt-2" :messages="$errors->updatePassword->get('current_password')" />
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                        New Password
                    </label>
                    <input id="password" name="password" type="password" autocomplete="new-password" class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                    <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password')" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-[11px] font-bold uppercase tracking-widest text-[#555] mb-2">
                        Confirm New Password
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] placeholder-[#AAA] focus:outline-none focus:border-[#00635D] focus:ring-1 focus:ring-[#00635D] transition">
                    <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password_confirmation')" />
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="bg-[#382110] text-white text-sm font-bold px-6 py-2.5 rounded hover:bg-[#2A180C] transition-colors">
                        Save Account Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div x-show="showDelete" 
         @open-delete-modal.window="showDelete = true"
         style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @click.away="showDelete = false" class="bg-white rounded-lg border border-[#DDD8CC] shadow-xl p-6 max-w-sm w-full mx-4">
            <h2 class="text-lg font-bold text-[#382110] mb-2">Delete Account?</h2>
            <p class="text-sm text-[#555] mb-5 leading-relaxed">
                This will permanently delete your account and all associated data. This action cannot be undone.
            </p>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="mb-4">
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" placeholder="Password" class="w-full px-3 py-2 text-sm border border-[#C8C0B0] rounded bg-white text-[#333] focus:outline-none focus:border-red-500 transition">
                    <x-input-error class="mt-2" :messages="$errors->userDeletion->get('password')" />
                </div>
                
                <div class="flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 py-2 border border-[#C8C0B0] rounded text-sm font-semibold text-[#555] hover:bg-[#F4F1EA] transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-semibold transition-colors">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- If there are deletion errors, reopen the modal -->
@if ($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('init', () => {
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        });
    });
</script>
@endif
@endsection
