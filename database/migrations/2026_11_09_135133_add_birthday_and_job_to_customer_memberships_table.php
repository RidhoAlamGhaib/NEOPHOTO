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
        Schema::table('customer_memberships', function (Blueprint $table) {
            $table->date('birthday')->nullable();
            $table->string('job')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_memberships', function (Blueprint $table) {
            $table->dropColumn(['birthday', 'job']);
        });
    }
};
