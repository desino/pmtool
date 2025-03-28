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
        Schema::table('planning_assignments', function (Blueprint $table) {
            $table->bigInteger('project_id')->after('initiative_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planning_assignments', function (Blueprint $table) {
            $table->dropColumn('project_id');
        });
    }
};
