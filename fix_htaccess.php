<?php
$content = "<IfModule mod_rewrite.c>\n";
$content .= "    RewriteEngine On\n\n";
$content .= "    RewriteCond %{HTTPS} !=on\n";
$content .= "    RewriteCond %{HTTP:X-Forwarded-Proto} !https\n";
$content .= "    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\n\n";
$content .= "    RewriteCond %{REQUEST_URI} !^/public/\n";
$content .= "    RewriteCond %{DOCUMENT_ROOT}/public%{REQUEST_URI} -f [OR]\n";
$content .= "    RewriteCond %{DOCUMENT_ROOT}/public%{REQUEST_URI} -d\n";
$content .= "    RewriteRule ^(.*)$ /public/\$1 [L]\n\n";
$content .= "    RewriteCond %{REQUEST_URI} !^/public/\n";
$content .= "    RewriteRule ^(.*)$ /public/index.php [L]\n";
$content .= "</IfModule>\n";
file_put_contents('.htaccess', $content);
echo "Fixed .htaccess\n";
echo file_get_contents('.htaccess');
