<?php

use App\Models\Auditorium;
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
        Schema::create('seating_arrangements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Auditorium::class)->constrained();
            $table->string('row');
            $table->unsignedInteger('seat_number');
            $table->string('seat_type');
            $table->unsignedInteger('x_position');
            $table->unsignedInteger('y_position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_arrangements');
    }
};
