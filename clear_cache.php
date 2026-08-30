<?php
/**
 * Route and Config Cache Clear Script
 * Access this via browser to clear Laravel caches after code updates
 */

$token = $_GET['token'] ?? '';
$expectedToken = 'clear_cache_2024';

if ($token !== $expectedToken) {
    http_response_code(403);
    die('Access denied. Invalid token.');
}

echo "<h2>Laravel Cache Clear</h2>";
echo "<pre>";

// Change to project directory
$projectDir = __DIR__;
chdir($projectDir);

// Function to run artisan command
function runArtisan($command) {
    $output = [];
    $returnCode = 0;
    exec("php artisan {$command} 2>&1", $output, $returnCode);
    echo "Command: php artisan {$command}\n";
    echo "Output: " . implode("\n", $output) . "\n";
    echo "Return code: {$returnCode}\n";
    echo str_repeat("-", 50) . "\n";
    return $returnCode === 0;
}

// Clear route cache
echo "\n=== Clearing Route Cache ===\n";
runArtisan('route:clear');

// Clear config cache
echo "\n=== Clearing Config Cache ===\n";
runArtisan('config:clear');

// Clear view cache
echo "\n=== Clearing View Cache ===\n";
runArtisan('view:clear');

// Clear application cache
echo "\n=== Clearing Application Cache ===\n";
runArtisan('cache:clear');

// Optional: Re-cache for production (comment out if not needed)
// echo "\n=== Re-caching Routes ===\n";
// runArtisan('route:cache');
// echo "\n=== Re-caching Config ===\n";
// runArtisan('config:cache');

echo "\n=== Done! ===\n";
echo "</pre>";
echo "<p><a href='/admin/products'>Go to Admin Products</a></p>";
