#!/bin/bash
cd ~/domains/costikyan.mindspire.org/public_html
PHP=/opt/alt/php83/usr/bin/php
cat > /tmp/run_redist.php << 'PHPEOF'
<?php $_GET["confirm"]=1; require "redistribute_images.php";
PHPEOF
$PHP /tmp/run_redist.php
