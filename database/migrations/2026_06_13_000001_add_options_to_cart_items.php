<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'is_sample')) {
                $table->boolean('is_sample')->default(false)->after('color');
            }
            if (!Schema::hasColumn('cart_items', 'custom_width')) {
                $table->decimal('custom_width', 8, 2)->nullable()->after('size');
            }
            if (!Schema::hasColumn('cart_items', 'custom_length')) {
                $table->decimal('custom_length', 8, 2)->nullable()->after('custom_width');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['is_sample', 'custom_width', 'custom_length']);
        });
    }
};
