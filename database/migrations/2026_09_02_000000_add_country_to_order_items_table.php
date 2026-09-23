<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Cuma dipake kalo item-nya produk ID/VISA. Nama negara,
            // cocok sama kolom `country` di tabel passport_photo_sizes.
            $table->string('country')->nullable()->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
