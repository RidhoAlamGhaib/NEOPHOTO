<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convert money columns that were stored as VARCHAR into DECIMAL(12,2).
 *
 * IMPORTANT: run on a backed-up database. The data-cleaning step below strips
 * any non-numeric characters (currency symbols, thousand separators, spaces)
 * before the type change so values like "Rp 40.000" or "40,000" do not cast
 * to 0 or 40.00. Verify the result after migrating.
 */
return new class extends Migration
{
    /**
     * @var array<string, string> table => money column
     */
    private array $columns = [
        'products' => 'price',
        'additionals' => 'price',
        'promos' => 'diskon',
        'orders' => 'total',
        'penjualans' => 'harga',
    ];

    public function up(): void
    {
        // 1) Normalise existing string values to a clean numeric string.
        foreach ($this->columns as $table => $column) {
            if (! Schema::hasColumn($table, $column)) {
                continue;
            }

            // Keep digits, one leading minus, and a single decimal point.
            DB::statement("
                UPDATE `{$table}`
                SET `{$column}` = CAST(
                    NULLIF(REGEXP_REPLACE(`{$column}`, '[^0-9.-]', ''), '') AS DECIMAL(12,2)
                )
                WHERE `{$column}` IS NOT NULL
            ");
        }

        // 2) Change the column types.
        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('price', 12, 2)->default(0)->change();
        });

        Schema::table('additionals', function (Blueprint $table): void {
            $table->decimal('price', 12, 2)->default(0)->change();
        });

        Schema::table('promos', function (Blueprint $table): void {
            $table->decimal('diskon', 12, 2)->default(0)->change();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('total', 12, 2)->nullable()->change();
        });

        Schema::table('penjualans', function (Blueprint $table): void {
            $table->decimal('harga', 12, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('price')->change();
        });

        Schema::table('additionals', function (Blueprint $table): void {
            $table->string('price')->change();
        });

        Schema::table('promos', function (Blueprint $table): void {
            $table->string('diskon')->change();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->string('total', 11)->nullable()->change();
        });

        Schema::table('penjualans', function (Blueprint $table): void {
            $table->string('harga')->change();
        });
    }
};
