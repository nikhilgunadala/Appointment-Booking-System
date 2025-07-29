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
    Schema::table('appointments', function (Blueprint $table) {
        // Add a column for the appointment reason/notes
        $table->text('reason')->nullable()->after('doctor_specialty');

        // Modify the existing status column to add the 'confirmed' option
        $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled'])->default('scheduled')->change();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            //
        });
    }
};
