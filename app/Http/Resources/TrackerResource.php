<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $now = now();
        $currentStart = $this->current_start_at;
        $currentDurationSeconds = $currentStart ? $currentStart->diffInSeconds($now) : 0;

        // Calculate Effective Longest Streak
        $effectiveLongestSeconds = max($currentDurationSeconds, $this->stored_longest_streak_seconds);

        // Helper to format any seconds into the word-ified string using the model's logic
        $formatSeconds = function ($seconds) {
            if ($seconds === 0)
                return 'Not started';

            $tempStart = now()->subSeconds($seconds);
            $nowForDiff = now();

            $years = (int) $tempStart->diffInYears($nowForDiff);
            if ($years > 0)
                return $years . ' ' . \Illuminate\Support\Str::plural('year', $years);

            $months = (int) $tempStart->diffInMonths($nowForDiff);
            if ($months > 0)
                return $months . ' ' . \Illuminate\Support\Str::plural('month', $months);

            $weeks = (int) $tempStart->diffInWeeks($nowForDiff);
            if ($weeks > 0)
                return $weeks . ' ' . \Illuminate\Support\Str::plural('week', $weeks);

            $days = (int) $tempStart->diffInDays($nowForDiff);
            if ($days > 0)
                return $days . ' ' . \Illuminate\Support\Str::plural('day', $days);

            $hours = (int) $tempStart->diffInHours($nowForDiff);
            if ($hours > 0)
                return $hours . ' ' . \Illuminate\Support\Str::plural('hour', $hours);

            $minutes = (int) $tempStart->diffInMinutes($nowForDiff);
            if ($minutes > 0)
                return $minutes . ' ' . \Illuminate\Support\Str::plural('minute', $minutes);

            return $seconds . ' ' . \Illuminate\Support\Str::plural('second', $seconds);
        };

        return [
            'id' => $this->id,
            'label' => $this->name,
            'current_streak' => [
                'raw_seconds' => $currentDurationSeconds,
                'human' => $this->readable_duration,
            ],
            'longest_streak' => [
                'raw_seconds' => $effectiveLongestSeconds,
                'human' => $formatSeconds($effectiveLongestSeconds),
            ]
        ];
    }
}
