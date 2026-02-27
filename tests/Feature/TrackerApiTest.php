<?php

use App\Models\Tracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('index returns a list of trackers', function () {
    Tracker::factory()->count(3)->create();

    $response = $this->getJson('/api/trackers');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('reset updates the tracker start time', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-05 12:00:00'));

    $tracker = Tracker::factory()->create([
        'current_start_at' => now()->subDays(5)
    ]);

    $response = $this->postJson("/api/trackers/{$tracker->id}/reset");

    $response->assertStatus(200);
    $this->assertDatabaseHas('trackers', [
        'id' => $tracker->id,
        'current_start_at' => '2026-01-05 12:00:00'
    ]);
});

test('reset with custom date works correctly', function () {
    $tracker = Tracker::factory()->create();

    $response = $this->postJson("/api/trackers/{$tracker->id}/reset", [
        'custom_date' => '2025-12-01 10:00:00'
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('trackers', [
        'id' => $tracker->id,
        'current_start_at' => '2025-12-01 10:00:00'
    ]);
});

test('undo reverts the tracker state', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-05 12:00:00'));

    $tracker = Tracker::factory()->create([
        'current_start_at' => now(),
        'previous_start_at' => now()->subDays(2),
        'stored_longest_streak_seconds' => 172800,
        'previous_longest_streak_seconds' => 0
    ]);

    $response = $this->postJson("/api/trackers/{$tracker->id}/undo");

    $response->assertStatus(200);
    $this->assertDatabaseHas('trackers', [
        'id' => $tracker->id,
        'current_start_at' => '2026-01-03 12:00:00',
        'previous_start_at' => null
    ]);
});
