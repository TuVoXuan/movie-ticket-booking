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
        Schema::table('seating_arrangements', function (Blueprint $table) {
            $table->unsignedInteger('x_position')->change();
            $table->string('y_position')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seating_arrangements', function (Blueprint $table) {
            $table->string('x_position')->change();
            $table->unsignedInteger('y_position')->change();
        });
    }
};
