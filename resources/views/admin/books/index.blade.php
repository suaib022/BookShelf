@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">Manage Books</h1>
        <div class="flex items-center gap-3">
            <button type="button" id="bulkDeleteBtn" onclick="confirmBulkDelete()" class="hidden px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded hover:opacity-90 transition-opacity shadow-sm">
                Delete Selected
            </button>
            <a href="{{ route('admin.books.create') }}" class="px-4 py-2 bg-[#5C7A3E] text-white text-sm font-semibold rounded hover:opacity-90 transition-opacity shadow-sm">
                + Add Book
            </a>
        </div>
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
                        <th class="px-6 py-3 w-12 text-center text-[10px] font-bold uppercase tracking-widest text-[#555]">
                            <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)" class="rounded border-gray-300 text-[#00635D] shadow-sm focus:border-[#00635D] focus:ring focus:ring-[#00635D] focus:ring-opacity-50">
                        </th>
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
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" onchange="updateBulkDeleteButton()" class="book-checkbox rounded border-gray-300 text-[#00635D] shadow-sm focus:border-[#00635D] focus:ring focus:ring-[#00635D] focus:ring-opacity-50">
                        </td>
                        <td class="px-6 py-4">
                            <img src="{{ $book->cover_url ? (str_starts_with($book->cover_url, 'http') ? $book->cover_url : Storage::url($book->cover_url)) : 'https://placehold.co/32x48?text=No+Cover' }}" alt="{{ $book->title }}" class="w-8 h-12 object-cover rounded shadow-sm">
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
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline-block" onsubmit="confirmDelete(event, this);">
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

    <!-- Hidden form for bulk delete -->
    <form id="bulkDeleteForm" action="{{ route('admin.books.bulkDestroy') }}" method="POST" class="hidden">
        @csrf
    </form>
    @if($books->hasPages())
        <div class="mt-6">
            {{ $books->links() }}
        </div>
    @endif
</div>

<!-- Custom Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 mx-4">
        <h3 class="text-lg font-bold text-[#382110] mb-2">Confirm Deletion</h3>
        <p class="text-sm text-[#555] mb-6">Are you sure you want to delete this book? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" type="button" class="px-4 py-2 text-sm font-semibold text-[#555] bg-gray-200 rounded hover:bg-gray-300 transition-colors">Cancel</button>
            <button id="confirmDeleteBtn" type="button" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded hover:bg-red-700 transition-colors">Delete Book</button>
        </div>
    </div>
</div>

<script>
    let formToSubmit = null;

    function confirmDelete(event, form) {
        event.preventDefault();
        formToSubmit = form;
        const modalText = document.querySelector('#deleteModal p');
        modalText.innerText = 'Are you sure you want to delete this book? This action cannot be undone.';
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function confirmBulkDelete() {
        formToSubmit = document.getElementById('bulkDeleteForm');
        
        // Clear previous inputs except CSRF
        formToSubmit.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
        
        const checkboxes = document.querySelectorAll('.book-checkbox:checked');
        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'book_ids[]';
            input.value = cb.value;
            formToSubmit.appendChild(input);
        });

        const count = checkboxes.length;
        const modalText = document.querySelector('#deleteModal p');
        modalText.innerText = `Are you sure you want to delete ${count} selected books? This action cannot be undone.`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function toggleAllCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.book-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateBulkDeleteButton();
    }

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.book-checkbox:checked').length;
        const btn = document.getElementById('bulkDeleteBtn');
        if (checkedCount > 0) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
        
        // Update selectAll checkbox state
        const allCount = document.querySelectorAll('.book-checkbox').length;
        const selectAllCb = document.getElementById('selectAll');
        if (selectAllCb) {
            selectAllCb.checked = (checkedCount === allCount && allCount > 0);
        }
    }

    function closeDeleteModal() {
        formToSubmit = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (formToSubmit) {
            formToSubmit.submit();
        }
    });
</script>
@endsection
