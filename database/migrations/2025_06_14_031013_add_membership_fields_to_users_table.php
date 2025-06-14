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
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_type')->default('trial');
            $table->integer('max_open_cases')->default(3);
            $table->timestamp('trial_start_date')->nullable();
            $table->timestamp('trial_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_type', 'max_open_cases', 'trial_start_date', 'trial_end_date']);
        });
    }
};
