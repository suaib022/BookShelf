<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_books' => Book::count(),
            'total_reviews' => Review::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
