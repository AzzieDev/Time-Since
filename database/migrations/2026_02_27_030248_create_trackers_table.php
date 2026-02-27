<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trackers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('current_start_at')->nullable();
            $table->unsignedBigInteger('stored_longest_streak_seconds')->default(0);
            $table->timestamp('previous_start_at')->nullable();
            $table->unsignedBigInteger('previous_longest_streak_seconds')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackers');
    }
};
