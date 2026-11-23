<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $productId = max(15, (int) DB::table('products')->max('id') + 1);
        $product = [
            'name' => 'Paket Khusus BTS',
            'type' => '107',
            'description' => 'Arirang, Snap it!',
            'price' => '65000',
            'img' => '2lmbrS.png',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('products', 'status')) {
            $product['status'] = 'active';
        }

        DB::table('products')->insert(array_merge(['id' => $productId], $product));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')
            ->where('name', 'Paket Khusus BTS')
            ->where('type', '107')
            ->delete();
    }
};
