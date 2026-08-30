<?php
// This script fixes php.ini for Laravel
$iniFile = 'C:\\php\\php.ini';

if (!file_exists($iniFile)) {
    copy('C:\\php\\php.ini-development', $iniFile);
    echo "Created php.ini from development template\n";
}

$content = file_get_contents($iniFile);

// Set extension directory
$content = str_replace(';extension_dir = "ext"', 'extension_dir = "C:\\\\php\\\\ext"', $content);

// Enable required extensions
$extensions = ['openssl', 'pdo_sqlite', 'sqlite3', 'mbstring', 'curl', 'fileinfo', 'gd'];
foreach ($extensions as $ext) {
    $content = str_replace(";extension=$ext", "extension=$ext", $content);
}

file_put_contents($iniFile, $content);
echo "✅ php.ini configured!\n";
echo "Extensions enabled: " . implode(', ', $extensions) . "\n";
