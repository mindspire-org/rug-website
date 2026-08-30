<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavedEstimate;
use App\Models\RoomVisualization;
use App\Models\SampleRequest;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function estimates()
    {
        $estimates = SavedEstimate::with(['user', 'product'])->latest()->paginate(20);
        return view('admin.submissions.estimates', compact('estimates'));
    }

    public function visualizations()
    {
        $visualizations = RoomVisualization::with(['user', 'product'])->latest()->paginate(20);
        return view('admin.submissions.visualizations', compact('visualizations'));
    }

    public function samples()
    {
        $samples = SampleRequest::with(['user', 'product'])->latest()->paginate(20);
        return view('admin.submissions.samples', compact('samples'));
    }
}
