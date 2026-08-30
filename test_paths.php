<?php
// Upload this to public_html/test_paths.php and access via browser

echo "<h2>Path Debug</h2>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br><br>";

echo "<h3>Checking paths:</h3>";

$paths = [
    'images/cover.jpg',
    'public/images/cover.jpg',
    '/images/cover.jpg',
    '/public/images/cover.jpg',
];

foreach ($paths as $p) {
    $full = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($p, '/');
    $exists = file_exists($full);
    echo "$p → $full: " . ($exists ? "EXISTS" : "NOT FOUND") . "<br>";
}

echo "<br><h3>Files in public/images/:</h3>";
$dir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/';
if (is_dir($dir)) {
    $files = glob($dir . '*');
    foreach (array_slice($files, 0, 5) as $f) {
        echo basename($f) . "<br>";
    }
} else {
    echo "Directory not found: $dir";
}
