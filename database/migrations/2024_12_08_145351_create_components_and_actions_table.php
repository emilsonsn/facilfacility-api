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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id')->nullable();
            $table->string('group')->nullable();
            $table->string('uniformat')->nullable();
            $table->string('name')->nullable();
            $table->string('time_left_by_condition')->nullable();
            $table->string('condition')->nullable();
            $table->string('year_installed')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unity')->nullable();
            $table->string('time_left_by_lifespan')->nullable();
            $table->string('coast')->nullable();
            $table->string('currency')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('facility_id')->references('id')->on('facilities');
        });

        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('component_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->date('date')->nullable();
            $table->string('category')->nullable();
            $table->string('condition')->nullable();
            $table->string('priority')->nullable();
            $table->string('frequency')->nullable();
            $table->string('coast')->nullable();
            $table->string('curracy')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('component_id')->references('id')->on('components');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
        Schema::dropIfExists('components');
    }
};
