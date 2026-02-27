<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tracker;
use App\Http\Resources\TrackerResource;
use Carbon\Carbon;

class TrackerController extends Controller
{
    public function index()
    {
        $trackers = Tracker::all();
        return TrackerResource::collection($trackers);
    }

    public function reset(Request $request, Tracker $tracker)
    {
        $customDate = $request->input('custom_date')
            ? Carbon::parse($request->input('custom_date'))
            : null;

        $tracker->reset($customDate);

        return new TrackerResource($tracker->fresh());
    }

    public function undo(Tracker $tracker)
    {
        $tracker->undo();

        return new TrackerResource($tracker->fresh());
    }
}
