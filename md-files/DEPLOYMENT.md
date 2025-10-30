# Laravel Dashboard Deployment Guide

This document describes how we deployed multiple Laravel dashboard applications on Ubuntu 22.04 server (147.93.56.130).

## Server Environment

- **Operating System**: Ubuntu 22.04 LTS
- **Web Server**: Nginx 1.18.0
- **PHP Version**: 8.4.13 (with php8.4-fpm)
- **SSL**: Let's Encrypt (Certbot)
- **Process Manager**: Systemd

## Deployed Applications

### 1. elixer-dashboard.qadi-tech.com
- **Repository**: https://github.com/Mosab97/dashboard.elixer.com.git
- **Domain**: https://elixer-dashboard.qadi-tech.com
- **Status**: ✅ Active with SSL
- **Document Root**: `/var/www/elixer-dashboard.qadi-tech.com/public`

### 2. dashboard.hananakellaw.com
- **Repository**: https://github.com/Mosab97/dashboard.hananakellaw.com.git
- **Domain**: dashboard.hananakellaw.com
- **Status**: 🔄 Deployed, awaiting DNS update for SSL
- **Document Root**: `/var/www/dashboard.hananakellaw.com/public`

## Deployment Process

### Step 1: Clone Repository
```bash
cd /var/www
git clone <repository-url> <domain-name>
chown -R www-data:www-data /var/www/<domain-name>
```

### Step 2: Install PHP Dependencies
```bash
cd /var/www/<domain-name>
composer install --no-dev --optimize-autoloader
```

### Step 3: Laravel Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Ensure logs directory exists
mkdir -p storage/logs
chown -R www-data:www-data storage/logs
```

### Step 4: Nginx Configuration
Create nginx virtual host configuration:

```nginx
server {
    server_name <domain-name>;
    
    # Document root for Laravel (points to public directory)
    root /var/www/<domain-name>/public;
    index index.php index.html index.htm;
    
    # phpMyAdmin configuration
    location /phpmyadmin {
        alias /usr/share/phpmyadmin;
        index index.php index.html index.htm;
        
        location ~ ^/phpmyadmin/(.+\.php)$ {
            alias /usr/share/phpmyadmin/$1;
            fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
        
        location ~* ^/phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
            alias /usr/share/phpmyadmin/$1;
            expires 30d;
        }
    }
    
    # Laravel-specific configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to .htaccess files
    location ~ /\.ht {
        deny all;
    }
    
    # Prevent access to sensitive files
    location ~ /\.(env|git) {
        deny all;
    }

    listen 80;
}
```

### Step 5: Enable Site
```bash
# Create symlink to enable site
ln -s /etc/nginx/sites-available/<domain-name> /etc/nginx/sites-enabled/

# Test nginx configuration
nginx -t

# Reload nginx
systemctl reload nginx
```

### Step 6: SSL Certificate (Let's Encrypt)
```bash
certbot --nginx -d <domain-name> --non-interactive --agree-tos --email mosab.ibrahim.ib@gmail.com
```

## PHP Extensions Installed

During deployment, the following PHP extensions were installed:

- `php8.4-curl` - For HTTP requests and Composer
- `php8.4-intl` - For internationalization features
- `php8.4-mysql` - For database connectivity
- `php8.4-xml` - For XML processing
- `php8.4-gd` - For image processing
- `php8.4-mbstring` - For multibyte string handling
- `php8.4-zip` - For ZIP archive handling

## File Structure

Each deployed application follows this structure:
```
/var/www/<domain-name>/
├── app/                 # Laravel application logic
├── bootstrap/           # Framework bootstrap files
├── config/              # Configuration files
├── database/            # Database migrations and seeds
├── public/              # Web server document root
├── resources/           # Views, assets, lang files
├── routes/              # Route definitions
├── storage/             # Logs, cache, sessions, uploads
├── tests/               # Automated tests
├── vendor/              # Composer dependencies
├── .env                 # Environment configuration
├── artisan              # Laravel command line interface
├── composer.json        # PHP dependencies
└── README.md            # Project documentation
```

## Permissions

- **Application files**: Owner `www-data:www-data`, Permission `644` for files, `755` for directories
- **Storage directory**: Permission `775` with `www-data:www-data` ownership
- **Bootstrap/cache**: Permission `775` with `www-data:www-data` ownership
- **Log files**: Created automatically by Laravel with proper permissions

## SSL Configuration

SSL certificates are automatically configured by Certbot and include:
- Automatic HTTP to HTTPS redirects
- Modern SSL configuration
- Automatic certificate renewal via cron job

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Laravel Log Permission Issues**:
   ```bash
   mkdir -p storage/logs
   chown -R www-data:www-data storage/logs
   chmod -R 775 storage/logs
   ```

3. **SSL Certificate Fails**:
   - Ensure domain DNS points to server IP (147.93.56.130)
   - Check that port 80/443 are open in firewall
   - Verify nginx configuration is correct

4. **PHP Extensions Missing**:
   ```bash
   apt install php8.4-<extension-name>
   systemctl restart php8.4-fpm
   ```

## Monitoring and Maintenance

### Log Locations
- **Nginx Access Logs**: `/var/log/nginx/access.log`
- **Nginx Error Logs**: `/var/log/nginx/error.log`
- **PHP-FPM Logs**: `/var/log/php8.4-fpm.log`
- **Laravel Logs**: `/var/www/<domain-name>/storage/logs/laravel.log`
- **SSL Certificates**: `/etc/letsencrypt/live/<domain-name>/`

### Regular Maintenance
- SSL certificates auto-renew via certbot cron job
- Monitor storage/logs directory size
- Keep PHP and system packages updated
- Regular database backups recommended

## Server Access

- **SSH**: `ssh root@147.93.56.130`
- **Web Server Config**: `/etc/nginx/sites-available/`
- **PHP Config**: `/etc/php/8.4/fpm/php.ini`
- **SSL Certs**: `/etc/letsencrypt/live/`

---
**Deployment completed on**: October 4, 2025  
**Last updated**: October 4, 2025  
**Deployed by**: AI Assistant following established patterns
