# Web Server Configuration Fix

## Problem
The SSO endpoints are returning 404 because the web server is not properly configured to serve the Laravel application.

## Current Structure
```
/www/wwwroot/sinta.dharmap.com/
├── laravel/          # Laravel application
│   ├── public/       # Laravel public directory
│   ├── app/
│   ├── routes/
│   └── ...
├── index.html        # Static HTML (currently served)
└── .htaccess
```

## Solution Options

### Option 1: Move Laravel to Document Root (Recommended)
```bash
# Backup current files
mv /www/wwwroot/sinta.dharmap.com/index.html /www/wwwroot/sinta.dharmap.com/index.html.bak
mv /www/wwwroot/sinta.dharmap.com/404.html /www/wwwroot/sinta.dharmap.com/404.html.bak
mv /www/wwwroot/sinta.dharmap.com/502.html /www/wwwroot/sinta.dharmap.com/502.html.bak

# Move Laravel public contents to document root
cp -r /www/wwwroot/sinta.dharmap.com/laravel/public/* /www/wwwroot/sinta.dharmap.com/
cp /www/wwwroot/sinta.dharmap.com/laravel/public/.htaccess /www/wwwroot/sinta.dharmap.com/

# Update index.php to point to correct Laravel path
sed -i 's|__DIR__\.'"'"'/../|__DIR__.'"'"'/laravel/|g' /www/wwwroot/sinta.dharmap.com/index.php
```

### Option 2: Configure Nginx/Apache for Subdirectory

#### For Nginx:
```nginx
server {
    listen 80;
    server_name sinta.dharmap.com;
    root /www/wwwroot/sinta.dharmap.com;
    
    # Laravel subdirectory
    location /laravel {
        alias /www/wwwroot/sinta.dharmap.com/laravel/public;
        try_files $uri $uri/ @laravel;
        
        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        }
    }
    
    location @laravel {
        rewrite /laravel/(.*)$ /laravel/index.php?/$1 last;
    }
}
```

#### For Apache (.htaccess in /www/wwwroot/sinta.dharmap.com/):
```apache
RewriteEngine On
RewriteRule ^laravel/(.*)$ laravel/public/$1 [L]
RewriteRule ^laravel$ laravel/public/ [L]
```

## Quick Fix (Temporary)
Create a symlink to make Laravel accessible:
```bash
ln -sf /www/wwwroot/sinta.dharmap.com/laravel/public /www/wwwroot/sinta.dharmap.com/sso-app
```

Then use URL: `http://sinta.dharmap.com/sso-app/sso/authorize`

## Update Client Configuration
After fixing the web server, update your client to use the correct base URL:

### If using Option 1 (Document Root):
```
Base URL: http://sinta.dharmap.com
Authorization URL: http://sinta.dharmap.com/sso/authorize
```

### If using Option 2 (Subdirectory):
```
Base URL: http://sinta.dharmap.com/laravel
Authorization URL: http://sinta.dharmap.com/laravel/sso/authorize
```

## Test After Configuration
```bash
curl "http://sinta.dharmap.com/sso/authorize?client_id=test&redirect_uri=http://example.com&response_type=code"
```

Should return either:
- Redirect to login (if not authenticated)
- JSON error response (if parameters invalid)
- NOT a 404 error
