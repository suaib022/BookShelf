<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BookShelf') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col md:flex-row">
            
            <!-- Sidebar -->
            <aside class="w-full md:w-64 bg-[#1a202c] text-white flex flex-col">
                <div class="h-16 flex items-center px-6 border-b border-gray-700">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-wider uppercase text-white">
                        BookShelf Admin
                    </a>
                </div>
                
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white transition' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.books.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.books.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white transition' }}">
                        Books
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white transition' }}">
                        Users
                    </a>
                </nav>
                
                <div class="p-4 border-t border-gray-700">
                    <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white transition">
                        &larr; Back to Site
                    </a>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                
                <!-- Topbar -->
                <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-end px-8 shadow-sm">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Log Out
                            </button>
                        </form>
                    </div>
                </header>
                
                <!-- Page Content -->
                <main class="flex-1 p-8 overflow-y-auto">
                    @yield('content')
                </main>
                
            </div>
            
        </div>
    </body>
</html>
