<?php
echo "PHP Version: " . phpversion() . "\n";
echo "INI File: " . (php_ini_loaded_file() ?: "NONE") . "\n";
echo "Extensions: " . implode(", ", get_loaded_extensions()) . "\n";
