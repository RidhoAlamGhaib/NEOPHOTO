<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convert quantity columns stored as VARCHAR into integers.
 *
 * Run on a backed-up database. Non-numeric values are coerced to 0 first.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('order_item_addons', 'qty')) {
            DB::statement("
                UPDATE `order_item_addons`
                SET `qty` = CAST(NULLIF(REGEXP_REPLACE(`qty`, '[^0-9]', ''), '') AS UNSIGNED)
            ");

            Schema::table('order_item_addons', function (Blueprint $table): void {
                $table->unsignedInteger('qty')->default(0)->nullable()->change();
            });
        }

        if (Schema::hasColumn('penjualans', 'Jumlah')) {
            DB::statement("
                UPDATE `penjualans`
                SET `Jumlah` = CAST(NULLIF(REGEXP_REPLACE(`Jumlah`, '[^0-9]', ''), '') AS UNSIGNED)
            ");

            Schema::table('penjualans', function (Blueprint $table): void {
                $table->unsignedInteger('Jumlah')->default(0)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('order_item_addons', function (Blueprint $table): void {
            $table->string('qty')->nullable()->change();
        });

        Schema::table('penjualans', function (Blueprint $table): void {
            $table->string('Jumlah')->change();
        });
    }
};
