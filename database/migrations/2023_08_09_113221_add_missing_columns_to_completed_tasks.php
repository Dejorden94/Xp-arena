<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completed_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('completed_tasks', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('completed_tasks', 'description')) {
                $table->text('description');
            }
            if (!Schema::hasColumn('completed_tasks', 'experience')) {
                $table->unsignedBigInteger('experience');
            }
            if (!Schema::hasColumn('completed_tasks', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
            if (!Schema::hasColumn('completed_tasks', 'is_rejected')) {
                $table->boolean('is_rejected')->default(false);
            }
            if (!Schema::hasColumn('completed_tasks', 'approval_status')) {
                $table->string('approval_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('completed_tasks', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'experience', 'is_verified', 'is_rejected', 'approval_status']);
        });
    }
};
