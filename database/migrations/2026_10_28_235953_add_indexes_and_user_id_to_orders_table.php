<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add an owner reference and query indexes to the orders table.
 *
 * `user_id` records which authenticated user created the order so ownership
 * can be enforced in the checkout flow (closes the IDOR where any logged-in
 * cashier could open/pay any order id). It is nullable so existing rows are
 * untouched; the application must set it at order creation and check it on
 * access. No foreign key is added to avoid breaking historical null rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('customer_id');
                $table->index('user_id');
            }

            $table->index('status');
            $table->index('outlet');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['status']);
            $table->dropIndex(['outlet']);

            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
