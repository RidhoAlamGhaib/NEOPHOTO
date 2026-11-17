<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasCustomerId = Schema::hasColumn('customer_memberships', 'customer_id');
        $hasPhone = Schema::hasColumn('customer_memberships', 'phone');

        if ($hasCustomerId || ! $hasPhone) {
            Schema::table('customer_memberships', function (Blueprint $table) use ($hasCustomerId, $hasPhone) {
                if ($hasCustomerId) {
                    $table->dropForeign(['customer_id']);
                    $table->dropUnique(['customer_id']);
                    $table->dropColumn('customer_id');
                }

                if (! $hasPhone) {
                    $table->string('phone')->nullable()->unique();
                }
            });
        }
    }

    public function down(): void
    {
        $hasCustomerId = Schema::hasColumn('customer_memberships', 'customer_id');
        $hasPhone = Schema::hasColumn('customer_memberships', 'phone');

        Schema::table('customer_memberships', function (Blueprint $table) use ($hasCustomerId, $hasPhone) {
            if ($hasPhone) {
                $table->dropUnique(['phone']);
                $table->dropColumn('phone');
            }

            if (! $hasCustomerId) {
                $table->foreignId('customer_id')->nullable()->unique()->constrained('customers')->cascadeOnDelete();
            }
        });
    }
};
