<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('zip_prices')) {
            return;
        }
        Schema::create('zip_prices', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('zip_start', 10);
            $table->string('zip_end', 10);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['zip_start', 'zip_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zip_prices');
    }
};
