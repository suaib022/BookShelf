<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

class RecommendationController extends Controller
{
    public function index()
    {
        $recommendations = DB::table('recommendations')
            ->join('books', 'recommendations.book_id', '=', 'books.id')
            ->where('recommendations.user_id', auth()->id())
            ->orderByDesc('recommendations.score')
            ->select('books.*', 'recommendations.reason', 'recommendations.score')
            ->paginate(12);
            
        // We need the books as Eloquent models to use their relationships (like authors) easily
        $bookIds = $recommendations->pluck('id');
        $books = Book::with('authors')->whereIn('id', $bookIds)->get()->keyBy('id');
        
        return view('recommendations.index', compact('recommendations', 'books'));
    }
}
