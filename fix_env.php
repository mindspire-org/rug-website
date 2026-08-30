<?php
$env = file_get_contents('.env');
$env = str_replace('MAIL_FROM_ADDRESS="noreply@costikyan.mindspire.org"', 'MAIL_FROM_ADDRESS="info@costikyan.mindspire.org"', $env);
file_put_contents('.env', $env);
echo "MAIL_FROM_ADDRESS updated\n";
echo file_get_contents('.env');
