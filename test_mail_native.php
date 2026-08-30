<?php
$result = mail("test@example.com", "Hostinger Test", "Testing PHP mail function");
echo "mail() result: " . ($result ? "TRUE" : "FALSE") . "\n";
