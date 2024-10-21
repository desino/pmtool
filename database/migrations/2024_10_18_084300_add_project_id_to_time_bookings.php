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
        Schema::table('time_bookings', function (Blueprint $table) {
            $table->bigInteger('project_id')->nullable()->after('ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_bookings', function (Blueprint $table) {
            $table->dropColumn('project_id');
        });
    }
};