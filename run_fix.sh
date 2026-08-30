#!/bin/bash
cd ~/domains/costikyan.mindspire.org/public_html
PHP=/opt/alt/php83/usr/bin/php
echo '<?php $_GET["confirm"]=1; require "fix_images.php";' > /tmp/run_fix.php
$PHP /tmp/run_fix.php
