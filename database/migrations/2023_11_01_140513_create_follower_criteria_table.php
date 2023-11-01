<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follower_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_task_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->boolean('is_met')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follower_criteria');
    }
};
