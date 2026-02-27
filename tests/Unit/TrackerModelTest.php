<?php

use App\Models\Tracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('a tracker returns not started if current start is null', function () {
    $tracker = Tracker::factory()->create(['current_start_at' => null]);

    expect($tracker->readable_duration)->toBe('Not started');
});

test('a tracker wordifies the duration correctly', function () {
    // Freeze time for consistent testing -> e.g. strictly 5 days ago
    Carbon::setTestNow(Carbon::parse('2026-01-05 12:00:00'));

    $trackerDays = Tracker::factory()->create(['current_start_at' => now()->subDays(5)]);
    $trackerHours = Tracker::factory()->create(['current_start_at' => now()->subHours(10)]);
    $trackerMonths = Tracker::factory()->create(['current_start_at' => now()->subMonths(3)]);

    expect($trackerDays->readable_duration)->toBe('5 days');
    expect($trackerHours->readable_duration)->toBe('10 hours');
    expect($trackerMonths->readable_duration)->toBe('3 months');
});

test('reset method calculates and buffers the previous streak', function () {
    // Freeze time
    Carbon::setTestNow(Carbon::parse('2026-01-05 12:00:00'));

    // Create a tracker that has been running for exactly 2 days (172800 seconds)
    $tracker = Tracker::factory()->create([
        'current_start_at' => now()->subDays(2),
        'stored_longest_streak_seconds' => 0,
    ]);

    // Reset the tracker
    $tracker->reset();

    // The new start time should be now
    expect($tracker->current_start_at->toDateTimeString())->toBe(now()->toDateTimeString());

    // The previous buffer should hold the old values
    expect($tracker->previous_start_at->toDateTimeString())->toBe(now()->subDays(2)->toDateTimeString());

    // The longest streak should have updated to the 2 days in seconds
    expect($tracker->stored_longest_streak_seconds)->toBe(172800);
});

test('undo method reverts to buffered values', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-05 12:00:00'));

    $tracker = Tracker::factory()->create([
        'current_start_at' => now(),
        'stored_longest_streak_seconds' => 172800,
        'previous_start_at' => now()->subDays(2),
        'previous_longest_streak_seconds' => 0,
    ]);

    $tracker->undo();

    // The current start time should be reverted to the old start time (2 days ago)
    expect($tracker->current_start_at->toDateTimeString())->toBe(now()->subDays(2)->toDateTimeString());

    // The longest streak should revert to what it was before (0)
    expect($tracker->stored_longest_streak_seconds)->toBe(0);

    // The buffers should be cleared
    expect($tracker->previous_start_at)->toBeNull();
    expect($tracker->previous_longest_streak_seconds)->toBeNull();
});
