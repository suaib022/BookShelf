@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">Edit Book: {{ $book->title }}</h1>
        <a href="{{ route('admin.books.index') }}" class="text-sm font-semibold text-[#00635D] hover:underline">
            &larr; Back to Books
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8">
        <div class="col-span-1">
            <div class="bg-white p-8 rounded-md shadow-sm border border-[#DDD8CC]">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="w-full">
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Title <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                        </div>

                        <div class="mb-4">
                            <label for="subtitle" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Subtitle</label>
                            <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $book->subtitle) }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                        </div>

                        <div class="mb-4">
                            <label for="authors" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Authors <span class="text-red-500">*</span></label>
                            <select id="authors" name="authors[]" multiple required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" {{ in_array($author->id, old('authors', $bookAuthors)) ? 'selected' : '' }}>{{ $author->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="genres" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Genres <span class="text-red-500">*</span></label>
                            <select id="genres" name="genres[]" multiple required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ in_array($genre->id, old('genres', $bookGenres)) ? 'selected' : '' }}>{{ $genre->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Description <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="5" required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">{{ old('description', $book->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="isbn10" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">ISBN-10</label>
                                <input type="text" id="isbn10" name="isbn10" value="{{ old('isbn10', $book->isbn10) }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                            <div>
                                <label for="isbn13" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">ISBN-13</label>
                                <input type="text" id="isbn13" name="isbn13" value="{{ old('isbn13', $book->isbn13) }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="page_count" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Page Count</label>
                                <input type="number" id="page_count" name="page_count" value="{{ old('page_count', $book->page_count) }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                            <div>
                                <label for="published_date" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Publish Date</label>
                                <input type="date" id="published_date" name="published_date" value="{{ old('published_date', $book->published_date) }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                            <div>
                                <label for="language" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Language <span class="text-red-500">*</span></label>
                                <input type="text" id="language" name="language" value="{{ old('language', $book->language) }}" required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                        </div>

                        <div class="mb-8 p-4 bg-[#F4F1EA] border border-[#DDD8CC] rounded">
                            <label class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-4">Current Cover</label>
                            @if($book->cover_url)
                                <div class="mb-4">
                                    <img src="{{ str_starts_with($book->cover_url, 'http') ? $book->cover_url : Storage::url($book->cover_url) }}" alt="Cover" class="w-24 h-36 object-cover rounded shadow-sm border border-[#DDD8CC]">
                                </div>
                            @else
                                <p class="text-sm text-[#777] mb-4">No cover uploaded.</p>
                            @endif

                            <label for="cover_image_file" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Update Cover Image Upload</label>
                            <input type="file" id="cover_image_file" name="cover_image_file" accept="image/*" class="w-full mb-2">
                            <p class="text-xs text-[#777]">Upload a new image (Max 2MB). It will be hosted via ImgBB.</p>
                            
                            <div class="mt-4">
                                <label for="cover_url" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Or Update Image URL</label>
                                <input type="url" id="cover_url" name="cover_url" value="{{ old('cover_url', $book->cover_url) }}" placeholder="https://..." class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                        </div>

                        <div class="">
                            <button type="submit" class="px-6 py-3 bg-[#382110] text-white font-bold rounded shadow hover:bg-[#555] transition-colors">
                                Update Book
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect("#authors",{
            plugins: ['remove_button'],
            create: function(input) {
                return {
                    value: 'NEW::' + input,
                    text: input
                }
            },
        });
        new TomSelect("#genres",{
            plugins: ['remove_button'],
            create: function(input) {
                return {
                    value: 'NEW::' + input,
                    text: input
                }
            },
        });
    });
</script>
@endsection
