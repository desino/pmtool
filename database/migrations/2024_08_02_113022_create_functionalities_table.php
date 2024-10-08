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
        Schema::create('functionalities', function (Blueprint $table) {
            $table->id();
            $table->integer('section_id')->unsigned();
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('include_in_solution_design')->default(0);
            $table->unsignedBigInteger('order_no')->default(0);
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
        Schema::dropIfExists('functionalities');
    }
};
