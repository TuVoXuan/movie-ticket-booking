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
        Schema::table('films', function (Blueprint $table) {
            // $table->dropColumn('thumbnail');
            // $table->dropColumn('thumbnail_bg');
            $table->unsignedBigInteger('thumbnail')->nullable();
            $table->unsignedBigInteger('thumbnail_bg')->nullable();
            $table->foreign('thumbnail')->references('id')->on('files')->onDelete('cascade');
            $table->foreign('thumbnail_bg')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            //
        });
    }
};
