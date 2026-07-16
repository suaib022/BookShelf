@extends('layouts.app')

@section('content')
<div class="max-w-[1060px] mx-auto px-6 py-8">
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-[#382110] leading-tight">Content Moderation</h1>
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
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border-b-2 {{ request()->routeIs('admin.reports.*') ? 'border-[#00635D] text-[#00635D] font-bold' : 'border-transparent text-[#555] hover:text-[#333] hover:border-[#C8C0B0]' }} text-sm transition-colors">
            Reports
        </a>
    </div>

    <div class="bg-white rounded-md shadow-sm border border-[#DDD8CC] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#F4F1EA] border-b border-[#DDD8CC]">
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Reported Content</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Reporter</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Reason</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Date</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555]">Status</th>
                    <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-[#555] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#DDD8CC]">
                @forelse($reports as $report)
                    <tr class="hover:bg-[#F4F1EA] transition-colors">
                        <td class="px-6 py-4">
                            @if($report->reportable)
                                <p class="text-sm font-semibold text-[#382110]">{{ class_basename($report->reportable_type) }}</p>
                                <p class="text-xs text-[#555] line-clamp-2 mt-1">{{ $report->reportable->body }}</p>
                            @else
                                <span class="text-xs italic text-gray-500">Content removed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-[#555] text-sm">
                            {{ $report->reporter->name }}
                        </td>
                        <td class="px-6 py-4 text-[#555] text-sm">
                            {{ $report->reason }}
                        </td>
                        <td class="px-6 py-4 text-[#555] text-sm whitespace-nowrap">
                            {{ $report->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($report->status === 'pending')
                                <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                            @elseif($report->status === 'resolved')
                                <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800 rounded-full">Resolved</span>
                            @else
                                <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-gray-200 text-gray-600 rounded-full">Dismissed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if($report->status === 'pending')
                                <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="inline-block" onsubmit="return confirm('Resolve this report and delete the offending content?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-block px-3 py-1 text-xs font-semibold text-white bg-[#5C7A3E] border border-[#5C7A3E] rounded hover:bg-[#4a6332] transition-colors mr-2">Resolve</button>
                                </form>
                                <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST" class="inline-block" onsubmit="return confirm('Dismiss this report?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-block px-3 py-1 text-xs font-semibold text-[#888] border border-[#DDD8CC] bg-white rounded hover:bg-gray-50 hover:text-[#555] transition-colors">Dismiss</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic">No actions available</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-[#888]">
                            <p class="mb-2">No reports found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
