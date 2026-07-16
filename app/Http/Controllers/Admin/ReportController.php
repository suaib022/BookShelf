<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reporter', 'reportable'])->latest()->paginate(15);
        return view('admin.reports.index', compact('reports'));
    }

    public function resolve(Report $report)
    {
        // Delete the offending content
        if ($report->reportable) {
            $report->reportable->delete();
        }

        $report->update(['status' => 'resolved']);
        return back()->with('success', 'Report resolved and content removed.');
    }

    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);
        return back()->with('success', 'Report dismissed.');
    }
}
