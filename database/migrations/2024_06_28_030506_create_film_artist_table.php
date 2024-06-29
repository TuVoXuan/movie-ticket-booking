<?php

use App\Models\Artist;
use App\Models\Film;
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
        Schema::create('film_artist', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Film::class)->constrained();
            $table->foreignIdFor(Artist::class)->constrained();
            $table->string('artist_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_artist');
    }
};
