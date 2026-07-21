RewriteEngine On

# If the request is not for an existing file or directory, try adding .php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}.php -f
RewriteRule ^(.*)$ $1.php [L]

# If still not an existing file, route through index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Disable directory listing
Options -Indexes

# Block access to deployment webhook
<Files "deploy.php.webhook">
    Deny from all
</Files>

# Block access to .env file
<Files ".env">
    Deny from all
</Files>
