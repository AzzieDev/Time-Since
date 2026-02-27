<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tracker;
use App\Http\Resources\TrackerResource;
use Carbon\Carbon;

class TrackerController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/trackers",
     *      operationId="getTrackersList",
     *      tags={"Trackers"},
     *      summary="Get list of all trackers",
     *      description="Returns list of trackers with their streak statistics",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="label", type="string", example="Accident Free Days"),
     *                  @OA\Property(property="current_streak", type="object",
     *                      @OA\Property(property="seconds", type="integer", example=172800),
     *                      @OA\Property(property="human_readable", type="string", example="2 days")
     *                  ),
     *                  @OA\Property(property="longest_streak", type="object",
     *                      @OA\Property(property="seconds", type="integer", example=864000),
     *                      @OA\Property(property="human_readable", type="string", example="10 days")
     *                  )
     *              ))
     *          )
     *      )
     * )
     */
    public function index()
    {
        $trackers = Tracker::all();
        return TrackerResource::collection($trackers);
    }

    /**
     * @OA\Post(
     *      path="/api/trackers/{tracker}/reset",
     *      operationId="resetTracker",
     *      tags={"Trackers"},
     *      summary="Reset a tracker's current streak",
     *      description="Resets the timer for the given tracker and archives the previous streak for undo capabilities. Also updates the longest streak if the current streak beat the record.",
     *      @OA\Parameter(
     *          name="tracker",
     *          description="Tracker ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="custom_date", type="string", format="date-time", example="2026-02-27T10:00:00Z", description="Optional date to reset the timer to. Defaults to now if omitted.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful reset",
     *      ),
     *      @OA\Response(response=404, description="Tracker not found")
     * )
     */
    public function reset(Request $request, Tracker $tracker)
    {
        $customDate = $request->input('custom_date')
            ? Carbon::parse($request->input('custom_date'))
            : null;

        $tracker->reset($customDate);

        return new TrackerResource($tracker->fresh());
    }

    /**
     * @OA\Post(
     *      path="/api/trackers/{tracker}/undo",
     *      operationId="undoTrackerReset",
     *      tags={"Trackers"},
     *      summary="Undo a recent reset operation",
     *      description="Restores the tracker to its exact state before the last reset operation was triggered. This clears the undo buffer.",
     *      @OA\Parameter(
     *          name="tracker",
     *          description="Tracker ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful undo"
     *      ),
     *      @OA\Response(response=404, description="Tracker not found")
     * )
     */
    public function undo(Tracker $tracker)
    {
        $tracker->undo();

        return new TrackerResource($tracker->fresh());
    }
}
