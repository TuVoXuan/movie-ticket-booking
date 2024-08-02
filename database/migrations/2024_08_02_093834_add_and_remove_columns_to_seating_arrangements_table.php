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
            $table->dropColumn('row');
            $table->dropColumn('seat_number');

            $table->string('label')->nullable()->after('auditorium_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seating_arrangements', function (Blueprint $table) {
            $table->dropColumn('label');

            $table->unsignedInteger('seat_number')->after('auditorium_id');
            $table->string('row')->after('auditorium_id');
        });
    }
};
