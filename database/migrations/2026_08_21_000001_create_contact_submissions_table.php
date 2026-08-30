<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contact_submissions')) {
            Schema::create('contact_submissions', function (Blueprint $table) {
                $table->id();
                $table->string('type')->default('contact'); // contact | weave
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('zip')->nullable();
                $table->text('message')->nullable();
                $table->text('meta')->nullable(); // extra fields (style, dimensions) as JSON
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
};
