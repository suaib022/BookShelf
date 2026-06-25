@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">Add New Book</h1>
        <a href="{{ route('admin.books.index') }}" class="text-sm font-semibold text-[#00635D] hover:underline">
            &larr; Back to Books
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8">
        <div class="col-span-1">
                </div>
            </div>

            <!-- Main Form -->
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

                <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="w-full">
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Title <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                        </div>

                        <div class="mb-4">
                            <label for="subtitle" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Subtitle</label>
                            <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle') }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                        </div>

                        <div class="mb-4">
                            <label for="authors" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Authors <span class="text-red-500">*</span></label>
                            <select id="authors" name="authors[]" multiple required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D] h-32">
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" {{ in_array($author->id, old('authors', [])) ? 'selected' : '' }}>{{ $author->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="genres" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Genres <span class="text-red-500">*</span></label>
                            <select id="genres" name="genres[]" multiple required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D] h-32">
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ in_array($genre->id, old('genres', [])) ? 'selected' : '' }}>{{ $genre->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Description <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="5" required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="isbn10" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">ISBN-10</label>
                                <input type="text" id="isbn10" name="isbn10" value="{{ old('isbn10') }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                            <div>
                                <label for="isbn13" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">ISBN-13</label>
                                <input type="text" id="isbn13" name="isbn13" value="{{ old('isbn13') }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="page_count" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Page Count</label>
                                <input type="number" id="page_count" name="page_count" value="{{ old('page_count') }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                            <div>
                                <label for="published_date" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Publish Date</label>
                                <input type="date" id="published_date" name="published_date" value="{{ old('published_date') }}" class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                            <div>
                                <label for="language" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Language <span class="text-red-500">*</span></label>
                                <input type="text" id="language" name="language" value="{{ old('language', 'en') }}" required class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                            </div>
                        </div>

                        <div class="mb-8 p-4 bg-[#F4F1EA] border border-[#DDD8CC] rounded">
                            <label for="cover_image_file" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Cover Image Upload</label>
                            <input type="file" id="cover_image_file" name="cover_image_file" accept="image/*" class="w-full mb-2">
                            <p class="text-xs text-[#777]">Upload an image (Max 2MB). It will be hosted via ImgBB.</p>
                            
                            <div class="mt-4">
                                <label for="cover_url" class="block text-sm font-bold text-[#555] uppercase tracking-wider mb-2">Or Image URL</label>
                                <input type="url" id="cover_url" name="cover_url" value="{{ old('cover_url') }}" placeholder="https://..." class="w-full px-4 py-2 border border-[#DDD8CC] rounded focus:outline-none focus:border-[#00635D]">
                                <p class="text-xs text-[#777] mt-1">If using Google Books import, the URL will be placed here automatically.</p>
                            </div>
                        </div>

                        <div class="">
                            <button type="submit" class="px-6 py-3 bg-[#382110] text-white font-bold rounded shadow hover:bg-[#555] transition-colors">
                                Save Book
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    searchBtn.addEventListener('click', function() {
        const query = searchInput.value.trim();
        if (!query) return;

        searchBtn.textContent = 'Searching...';
        searchBtn.disabled = true;

        fetch(`{{ route('admin.books.search-google') }}?q=${encodeURIComponent(query)}`)
            .then(async res => {
                if (!res.ok) {
                    const errText = await res.text();
                    throw new Error(errText);
                }
                return res.json();
            })
            .then(data => {
                resultsContainer.innerHTML = '';
                resultsContainer.classList.remove('hidden');

                if (data.items && data.items.length > 0) {
                    data.items.forEach(item => {
                        const volume = item.volumeInfo;
                        const div = document.createElement('div');
                        div.className = 'flex items-center p-2 bg-white border border-[#DDD8CC] rounded cursor-pointer hover:bg-[#F9F8F6]';
                        
                        const thumb = volume.imageLinks?.thumbnail || 'https://placehold.co/32x48?text=No+Cover';
                        const title = volume.title || 'Unknown Title';
                        const authors = volume.authors ? volume.authors.join(', ') : '';

                        div.innerHTML = `
                            <img src="${thumb}" class="w-8 h-12 object-cover mr-4">
                            <div>
                                <p class="font-bold text-sm text-[#382110]">${title}</p>
                                <p class="text-xs text-[#555]">${authors}</p>
                            </div>
                        `;

                        div.addEventListener('click', function() {
                            document.getElementById('title').value = volume.title || '';
                            document.getElementById('subtitle').value = volume.subtitle || '';
                            document.getElementById('description').value = volume.description || '';
                            document.getElementById('page_count').value = volume.pageCount || '';
                            document.getElementById('language').value = volume.language || 'en';
                            
                            // Handling dates (Google might return 'YYYY', 'YYYY-MM', or 'YYYY-MM-DD')
                            let pubDate = volume.publishedDate || '';
                            if (pubDate.length === 4) pubDate += '-01-01';
                            if (pubDate.length === 7) pubDate += '-01';
                            document.getElementById('published_date').value = pubDate;
                            
                            if (volume.industryIdentifiers) {
                                volume.industryIdentifiers.forEach(id => {
                                    if (id.type === 'ISBN_10') document.getElementById('isbn10').value = id.identifier;
                                    if (id.type === 'ISBN_13') document.getElementById('isbn13').value = id.identifier;
                                });
                            }
                            
                            if (volume.imageLinks && volume.imageLinks.thumbnail) {
                                // use higher res if possible by replacing zoom=1 with zoom=2 or just remove zoom
                                let url = volume.imageLinks.thumbnail.replace('http:', 'https:');
                                document.getElementById('cover_url').value = url;
                            }

                            // Optional: trying to auto-select authors/genres is tricky because names must match perfectly
                            
                            if (volume.authors) {
                                volume.authors.forEach(author => {
                                    // check if author exists in options by text
                                    let option = Object.values(authorSelect.options).find(opt => opt.text.toLowerCase() === author.toLowerCase());
                                    if (option) {
                                        authorSelect.addItem(option.value);
                                    } else {
                                        authorSelect.addOption({value: 'NEW::' + author, text: author});
                                        authorSelect.addItem('NEW::' + author);
                                    }
                                });
                            }

                            if (volume.categories) {
                                volume.categories.forEach(category => {
                                    let option = Object.values(genreSelect.options).find(opt => opt.text.toLowerCase() === category.toLowerCase());
                                    if (option) {
                                        genreSelect.addItem(option.value);
                                    } else {
                                        genreSelect.addOption({value: 'NEW::' + category, text: category});
                                        genreSelect.addItem('NEW::' + category);
                                    }
                                });
                            }
                            
                            resultsContainer.classList.add('hidden');
                            searchInput.value = '';
                        });

                        resultsContainer.appendChild(div);
                    });
                } else {
                    resultsContainer.innerHTML = '<p class="text-sm p-2 text-[#777]">No results found.</p>';
                }
            })
            .catch(err => {
                resultsContainer.innerHTML = '<p class="text-sm p-2 text-red-500">Error fetching results. If you get a quota error, please add a GOOGLE_BOOKS_API_KEY to your .env file.</p>';
                resultsContainer.classList.remove('hidden');
            })
            .finally(() => {
                searchBtn.textContent = 'Search';
                searchBtn.disabled = false;
            });
    });
});
</script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    let authorSelect, genreSelect;

    document.addEventListener('DOMContentLoaded', function() {
        authorSelect = new TomSelect("#authors",{
            plugins: ['remove_button'],
            create: function(input) {
                return {
                    value: 'NEW::' + input,
                    text: input
                }
            },
        });
        genreSelect = new TomSelect("#genres",{
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
