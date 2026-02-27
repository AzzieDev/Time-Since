<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Carbon\Carbon;

class Tracker extends Model
{
    protected $fillable = [
        'name',
        'current_start_at',
        'stored_longest_streak_seconds',
        'previous_start_at',
        'previous_longest_streak_seconds',
    ];

    protected $casts = [
        'current_start_at' => 'datetime',
        'previous_start_at' => 'datetime',
    ];

    /**
     * Get the readable duration of the current streak.
     * Returns ONLY the single largest unit as a string.
     */
    public function getReadableDurationAttribute()
    {
        if (!$this->current_start_at) {
            return 'Not started';
        }

        $now = now();
        $start = $this->current_start_at;

        // Single largest unit check
        $years = $start->diffInYears($now);
        if ($years > 0)
            return $years . ' ' . Str::plural('year', $years);

        $months = $start->diffInMonths($now);
        if ($months > 0)
            return $months . ' ' . Str::plural('month', $months);

        $weeks = $start->diffInWeeks($now);
        if ($weeks > 0)
            return $weeks . ' ' . Str::plural('week', $weeks);

        $days = $start->diffInDays($now);
        if ($days > 0)
            return $days . ' ' . Str::plural('day', $days);

        $hours = $start->diffInHours($now);
        if ($hours > 0)
            return $hours . ' ' . Str::plural('hour', $hours);

        $minutes = $start->diffInMinutes($now);
        if ($minutes > 0)
            return $minutes . ' ' . Str::plural('minute', $minutes);

        $seconds = $start->diffInSeconds($now);
        return $seconds . ' ' . Str::plural('second', $seconds);
    }

    /**
     * Resets the tracker to a custom date (or now).
     * Saves the previous state to the undo buffer before updating.
     */
    public function reset(Carbon $customDate = null)
    {
        $now = $customDate ?? now();

        if ($this->current_start_at) {
            $durationSeconds = $this->current_start_at->diffInSeconds($now);

            // Save to undo buffer
            $this->previous_start_at = $this->current_start_at;
            $this->previous_longest_streak_seconds = $this->stored_longest_streak_seconds;

            // Update longest streak if applicable
            if ($durationSeconds > $this->stored_longest_streak_seconds) {
                $this->stored_longest_streak_seconds = $durationSeconds;
            }
        }

        // Reset current start
        $this->current_start_at = $now;
        $this->save();
    }

    /**
     * Undoes the last reset operation, reverting to the buffer state.
     */
    public function undo()
    {
        if ($this->previous_start_at !== null) {
            // Restore from undo buffer
            $this->current_start_at = $this->previous_start_at;
            $this->stored_longest_streak_seconds = $this->previous_longest_streak_seconds;

            // Clear undo buffer
            $this->previous_start_at = null;
            $this->previous_longest_streak_seconds = null;

            $this->save();
        }
    }
}
