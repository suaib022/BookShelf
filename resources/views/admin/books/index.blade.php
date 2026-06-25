@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">Manage Books</h1>
        <a href="{{ route('admin.books.create') }}" class="px-4 py-2 bg-[#5C7A3E] text-white text-sm font-semibold rounded hover:opacity-90 transition-opacity shadow-sm">
            + Add Book
        </a>
    </div>

    <!-- Admin Navigation Tabs -->
    <div class="flex border-b border-[#DDD8CC] mb-8">
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-[#00635D] text-[#00635D] font-bold' : 'border-transparent text-[#555] hover:text-[#333] hover:border-[#C8C0B0]' }} text-sm transition-colors">
            Overview
        </a>
        <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border-b-2 {{ request()->routeIs('admin.books.*') ? 'border-[#00635D] text-[#00635D] font-bold' : 'border-transparent text-[#555] hover:text-[#333] hover:border-[#C8C0B0]' }} text-sm transition-colors">
            Books
        </a>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-[#00635D] text-[#00635D] font-bold' : 'border-transparent text-[#555] hover:text-[#333] hover:border-[#C8C0B0]' }} text-sm transition-colors">
            Users
        </a>
    </div>

    <div class="bg-white rounded-md shadow-sm border border-[#DDD8CC] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#F4F1EA] border-b border-[#DDD8CC]">
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Cover</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Title</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Author(s)</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Rating</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#DDD8CC]">
                @forelse($books as $book)
                    <tr class="hover:bg-[#F4F1EA] transition-colors">
                        <td class="px-6 py-4">
                            <img src="{{ $book->cover_url ? Storage::url($book->cover_url) : 'https://placehold.co/32x48?text=No+Cover' }}" alt="{{ $book->title }}" class="w-8 h-12 object-cover rounded shadow-sm">
                        </td>
                        <td class="px-6 py-4 font-bold text-[#382110] text-sm">
                            {{ $book->title }}
                        </td>
                        <td class="px-6 py-4 text-[#555] text-sm">
                            {{ $book->authors->pluck('name')->join(', ') ?: 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 text-[#555] text-sm">
                            ★ {{ number_format($book->avg_rating, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.books.edit', $book) }}" class="inline-block px-3 py-1 text-xs font-semibold text-[#00635D] border border-[#00635D] rounded hover:bg-[#00635D] hover:text-white transition-colors mr-2">Edit</a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-xs font-semibold text-red-600 border border-red-600 rounded hover:bg-red-600 hover:text-white transition-colors">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-[#999] text-sm">
                            No books found in the catalog.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($books->hasPages())
        <div class="mt-6">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection
