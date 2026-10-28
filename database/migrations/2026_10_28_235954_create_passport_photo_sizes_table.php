<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create migration for passport_photo_sizes — the table has a model
 * (App\Models\PassportPhotoSize) but was previously created by hand with no
 * migration. Guarded by hasTable() so it is a no-op on databases that already
 * contain it.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('passport_photo_sizes')) {
            return;
        }

        Schema::create('passport_photo_sizes', function (Blueprint $table): void {
            $table->id();
            $table->string('country', 100);
            $table->enum('document_type', ['passport', 'visa', 'schengen_visa', 'other'])->default('passport');
            $table->decimal('width_cm', 4, 1);
            $table->decimal('height_cm', 4, 1);
            $table->unsignedInteger('width_px');
            $table->unsignedInteger('height_px');
            $table->unsignedTinyInteger('min_copies')->default(4);
            $table->string('background', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('country', 'idx_country');
            $table->index('document_type', 'idx_document_type');
            $table->index(['country', 'document_type'], 'idx_country_document');
            $table->index(['width_cm', 'height_cm'], 'idx_photo_size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passport_photo_sizes');
    }
};
