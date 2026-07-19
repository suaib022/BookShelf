<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityEvent;
use App\Services\RecommendationService;

class HomeController extends Controller
{
    public function index(RecommendationService $recommendationService)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $recommendationService->generateForUser($user);

            
            $shelves = $user->shelves()->whereIn('name', ['Read', 'Currently Reading', 'Want to Read'])->get()->keyBy('name');
            
            $currentlyReadingShelf = $shelves->get('Currently Reading');
            $wantToReadShelf = $shelves->get('Want to Read');
            $readShelf = $shelves->get('Read');
            
            $currentlyReadingBooks = $currentlyReadingShelf ? $currentlyReadingShelf->shelfBooks()->with('book.authors')->get() : collect();
            $wantToReadBooks = $wantToReadShelf ? $wantToReadShelf->shelfBooks()->with('book.authors')->get() : collect();
            
            $shelfCounts = [
                'Read' => $readShelf ? $readShelf->shelfBooks()->count() : 0,
                'Currently Reading' => $currentlyReadingBooks->count(),
                'Want to Read' => $wantToReadBooks->count(),
            ];
            
            $followingIds = $user->following()->pluck('users.id');
            
            $allEvents = ActivityEvent::with(['user', 'book.authors', 'targetUser'])
                ->whereIn('user_id', $followingIds)
                ->whereIn('type', ['rating', 'review', 'rate'])
                ->latest()
                ->get();
            
            // Merge rate+review for the same user+book into one card
            $merged = [];
            foreach ($allEvents as $event) {
                if (!$event->book) continue;
                $key = $event->user_id . '_' . $event->book_id;
                
                if (!isset($merged[$key])) {
                    $merged[$key] = $event;
                } else {
                    $existing = $merged[$key];
                    // Combine metadata from both events
                    $combinedMeta = array_merge($existing->metadata ?? [], $event->metadata ?? []);
                    
                    // Keep the latest event as the primary (it's already sorted desc)
                    // The first one we saw is the latest, so merge the older one's data into it
                    $existingMeta = $existing->metadata ?? [];
                    $eventMeta = $event->metadata ?? [];
                    $existing->metadata = array_merge($eventMeta, $existingMeta);
                    $merged[$key] = $existing;
                }
            }
            
            // Paginate manually
            $page = request()->get('page', 1);
            $perPage = 15;
            $mergedCollection = collect(array_values($merged));
            $activityEvents = new \Illuminate\Pagination\LengthAwarePaginator(
                $mergedCollection->forPage($page, $perPage),
                $mergedCollection->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
                
            $recommendations = \Illuminate\Support\Facades\DB::table('recommendations')
                ->join('books', 'recommendations.book_id', '=', 'books.id')
                ->where('recommendations.user_id', $user->id)
                ->orderByDesc('recommendations.score')
                ->select('books.*', 'recommendations.reason')
                ->limit(2)
                ->get();
                
            if ($recommendations->isNotEmpty()) {
                $bookIds = $recommendations->pluck('id');
                $recommendedBooks = \App\Models\Book::with('authors')->whereIn('id', $bookIds)->get()->keyBy('id');
                foreach ($recommendations as $rec) {
                    $rec->book = $recommendedBooks[$rec->id] ?? null;
                }
            }
                
            return view('home', compact('currentlyReadingBooks', 'wantToReadBooks', 'shelfCounts', 'activityEvents', 'recommendations'));
        }
        
        return view('home');
    }
}
