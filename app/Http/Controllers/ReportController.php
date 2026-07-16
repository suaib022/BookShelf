<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Review;
use App\Models\ReviewComment;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|string|in:review,comment',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|max:1000'
        ]);

        $type = $request->reportable_type === 'review' ? Review::class : ReviewComment::class;

        // Check if already reported by this user
        $exists = Report::where('reporter_id', auth()->id())
            ->where('reportable_type', $type)
            ->where('reportable_id', $request->reportable_id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reported this content.');
        }

        Report::create([
            'reporter_id' => auth()->id(),
            'reportable_type' => $type,
            'reportable_id' => $request->reportable_id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Thank you. The content has been reported to moderators.');
    }
}
