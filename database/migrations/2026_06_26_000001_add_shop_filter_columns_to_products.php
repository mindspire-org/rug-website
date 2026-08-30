<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('construction')->nullable()->after('style');
            $table->string('room_type')->nullable()->after('construction');
            $table->string('availability')->nullable()->after('room_type');
            $table->string('size_category')->nullable()->after('availability');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['construction', 'room_type', 'availability', 'size_category']);
        });
    }
};
