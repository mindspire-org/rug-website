<?php
$htaccess = '<IfModule mod_rewrite.c>' . "\n";
$htaccess .= '    RewriteEngine On' . "\n\n";
$htaccess .= '    RewriteCond %{HTTPS} !=on' . "\n";
$htaccess .= '    RewriteCond %{HTTP:X-Forwarded-Proto} !https' . "\n";
$htaccess .= '    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' . "\n\n";
$htaccess .= '    RewriteCond %{REQUEST_URI} ^/(images|css|js|storage|fonts|build)/ [OR]' . "\n";
$htaccess .= '    RewriteCond %{REQUEST_URI} ^/favicon\\.ico$ [OR]' . "\n";
$htaccess .= '    RewriteCond %{REQUEST_URI} ^/robots\\.txt$' . "\n";
$htaccess .= '    RewriteRule ^ public%{REQUEST_URI} [L]' . "\n\n";
$htaccess .= '    RewriteCond %{REQUEST_FILENAME} !-d' . "\n";
$htaccess .= '    RewriteCond %{REQUEST_FILENAME} !-f' . "\n";
$htaccess .= '    RewriteRule ^ index.php [L]' . "\n";
$htaccess .= '</IfModule>' . "\n";

file_put_contents('.htaccess', $htaccess);
echo "Root .htaccess updated successfully.\n";
echo "Contents:\n";
echo file_get_contents('.htaccess');
