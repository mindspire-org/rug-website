<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Product Filter Attributes table
        Schema::create('product_filter_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->enum('type', ['select', 'multiselect', 'text', 'number'])->default('select');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Product Filter Values table
        Schema::create('product_filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_filter_attribute_id')
                ->constrained('product_filter_attributes')
                ->onDelete('cascade');
            $table->string('value');
            $table->string('display_value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Pivot table for products and filter values
        Schema::create('product_filter_value_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('product_filter_value_id')
                ->constrained('product_filter_values')
                ->onDelete('cascade');
            $table->foreignId('product_filter_attribute_id')
                ->constrained('product_filter_attributes')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_filter_value_product');
        Schema::dropIfExists('product_filter_values');
        Schema::dropIfExists('product_filter_attributes');
    }
};
