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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->dateTime('release_date');
            $table->unsignedInteger('duration');
            $table->unsignedInteger('age_restricted')->nullable();
            $table->string('trailer')->nullable();
            $table->unsignedBigInteger('thumbnail')->nullable();
            $table->unsignedBigInteger('thumbnail_bg')->nullable();
            $table->string('title');
            $table->string('description');
            $table->string('code');
            $table->timestamps();

            $table->foreign('thumbnail')->references('id')->on('files')->onDelete('cascade');
            $table->foreign('thumbnail_bg')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
