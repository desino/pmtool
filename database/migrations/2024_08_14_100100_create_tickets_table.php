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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('asana_task_id')->nullable();
            $table->bigInteger('initiative_id')->nullable();
            $table->bigInteger('functionality_id')->nullable();
            $table->bigInteger('project_id')->nullable();
            $table->string('name');
            $table->longText('composed_name')->nullable();
            $table->integer('type');
            $table->float('initial_estimation_development_time', 8, 2);
            $table->longText('release_note')->nullable();
            $table->boolean('auto_wait_for_client_approval')->default(0);
            $table->integer('status')->nullable();
            $table->integer('macro_status')->nullable();
            $table->boolean('is_priority')->default(0);
            $table->boolean('is_visible')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
