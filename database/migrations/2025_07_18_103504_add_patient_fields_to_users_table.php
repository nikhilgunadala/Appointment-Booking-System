<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // In the up() method of your new migration file
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone_number')->nullable();
        $table->date('date_of_birth')->nullable();
        $table->string('gender')->nullable();
        $table->text('address')->nullable();
        $table->enum('role', ['patient', 'doctor', 'admin'])->default('patient');
        // We make them nullable in case you want a multi-step registration later.
        // The role defaults to 'patient' for all new registrations through this form.
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
