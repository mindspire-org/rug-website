<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix product_colors table columns for MySQL
        if (!Schema::hasColumn('product_colors', 'color_name')) {
            Schema::table('product_colors', function (Blueprint $table) {
                $table->string('color_name')->after('product_id');
            });
        }
        if (!Schema::hasColumn('product_colors', 'color_hex')) {
            Schema::table('product_colors', function (Blueprint $table) {
                $table->string('color_hex')->after('color_name');
            });
        }
    }

    public function down(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            $table->dropColumn(['color_name', 'color_hex']);
        });
    }
};
