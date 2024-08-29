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
        Schema::create('initiatives', function (Blueprint $table) {
            $table->id();
            $table->string('asana_project_id')->nullable();
            $table->bigInteger('client_id');
            $table->string('name');
            $table->bigInteger('ballpark_development_hours');
            $table->integer('status')->default(0);
            $table->text('share_point_url')->nullable();
            $table->bigInteger('functional_owner_id')->nullable();
            $table->bigInteger('technical_owner_id')->nullable();
            $table->bigInteger('quality_owner_id')->nullable();
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
        Schema::dropIfExists('initiatives');
    }
};
