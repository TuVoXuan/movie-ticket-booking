<?php

use App\Models\CinemaCompany;
use App\Models\Region;
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
        Schema::create('cinema_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->float('latitude');
            $table->float('longitude');
            $table->foreignIdFor(Region::class)->constrained();
            $table->foreignIdFor(CinemaCompany::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cinema_branches');
    }
};
