<?php
// run_migrate.php - Upload to public_html, visit once, then DELETE
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>Migration Fix</h2><pre>";

// Step 1: Mark existing migrations as run if their tables already exist
$existingMigrations = [
    '2024_05_26_100000_create_ecommerce_tables.php',
    '2024_05_31_000001_create_product_filter_tables.php',
];

foreach ($existingMigrations as $migration) {
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    if (!$exists) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => 1,
        ]);
        echo "Marked as run: {$migration}\n";
    } else {
        echo "Already tracked: {$migration}\n";
    }
}

// Step 2: Fix product_colors columns if missing
echo "\n--- Fixing product_colors ---\n";
if (!Schema::hasColumn('product_colors', 'color_name')) {
    Schema::table('product_colors', function ($table) {
        $table->string('color_name')->after('product_id');
    });
    echo "Added: color_name\n";
} else {
    echo "Already exists: color_name\n";
}

if (!Schema::hasColumn('product_colors', 'color_hex')) {
    Schema::table('product_colors', function ($table) {
        $table->string('color_hex')->after('color_name');
    });
    echo "Added: color_hex\n";
} else {
    echo "Already exists: color_hex\n";
}

// Step 3: Create product_dimension_prices if missing
echo "\n--- Checking product_dimension_prices ---\n";
if (!Schema::hasTable('product_dimension_prices')) {
    Schema::create('product_dimension_prices', function ($table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->string('label')->nullable();
        $table->decimal('width', 10, 2)->nullable();
        $table->decimal('length', 10, 2)->nullable();
        $table->string('shape')->default('rectangular');
        $table->decimal('price', 12, 2)->default(0);
        $table->decimal('sale_price', 12, 2)->nullable();
        $table->integer('stock')->default(0);
        $table->boolean('is_default')->default(false);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
    echo "Created: product_dimension_prices\n";
} else {
    echo "Already exists: product_dimension_prices\n";
}

// Step 4: Run remaining new migrations
echo "\n--- Running remaining migrations ---\n";
$status = Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();
echo "\nExit code: " . $status . "\n";

echo "</pre><p><b>Done!</b> You can now create products. <b>Delete this file immediately for security.</b></p>";

// Self-delete
@unlink(__FILE__);
