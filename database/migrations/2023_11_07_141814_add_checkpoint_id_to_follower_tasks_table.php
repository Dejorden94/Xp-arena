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
        Schema::table('follower_tasks', function (Blueprint $table) {
            // Voeg de checkpoint_id kolom toe
            $table->unsignedBigInteger('checkpoint_id')->nullable()->after('id');

            // Voeg de foreign key constraint toe
            $table->foreign('checkpoint_id')
                ->references('id')->on('checkpoints')
                ->onDelete('set null'); // Of 'cascade' als je wilt dat de verwijdering doorgaat naar de follower_tasks
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follower_tasks', function (Blueprint $table) {
            // Verwijder de foreign key constraint
            $table->dropForeign(['checkpoint_id']);

            // Verwijder de checkpoint_id kolom
            $table->dropColumn('checkpoint_id');
        });
    }
};
