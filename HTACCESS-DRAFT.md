RewriteEngine On

# If the request is not for an existing file or directory
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Route everything through index.php
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
