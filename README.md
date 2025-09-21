# Laravel Menu Restaurant Project

## Server Deployment Guide

### Prerequisites
- Ubuntu 22.04 Server
- Nginx web server
- PHP 8.2+ with required extensions
- MySQL/MariaDB database
- Composer
- SSL certificate (Let's Encrypt)

## Complete Deployment Steps

### 1. Environment Setup
```bash
# Create project directory
mkdir -p /var/www/menu-restaurants

# Clone the repository
git clone https://github.com/Mosab97/menu-resturant.git /var/www/menu-restaurants

# Navigate to project directory
cd /var/www/menu-restaurants
```

### 2. PHP 8.2 Installation
```bash
# Add PHP repository
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.2 and extensions
apt install php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-xml php8.2-xmlrpc php8.2-curl php8.2-gd php8.2-imagick php8.2-dev php8.2-imap php8.2-mbstring php8.2-opcache php8.2-soap php8.2-zip php8.2-intl -y

# Start and enable PHP-FPM
systemctl start php8.2-fpm
systemctl enable php8.2-fpm
```

### 3. Composer Installation
```bash
# Install Composer
apt install composer -y

# Install Laravel dependencies
composer install --optimize-autoloader --no-dev
```

### 4. Laravel Configuration
```bash
# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Update .env for production
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's|APP_URL=http://localhost|APP_URL=https://test.qadi-tech.com|' .env
```

### 5. Nginx Configuration
```bash
# Create nginx site configuration
cat > /etc/nginx/sites-available/test.qadi-tech.com << 'NGINX_EOF'
server {
    listen 80;
    server_name test.qadi-tech.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name test.qadi-tech.com;
    
    # SSL certificate
    ssl_certificate /etc/letsencrypt/live/test.qadi-tech.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/test.qadi-tech.com/privkey.pem;
    
    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Document root for Laravel
    root /var/www/menu-restaurants/public;
    index index.php index.html index.htm;
    
    # Laravel URL rewriting
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Security
    location ~ /\.ht {
        deny all;
    }
    
    location ~ /\.(env|git) {
        deny all;
    }
}
NGINX_EOF

# Enable site and reload nginx
ln -s /etc/nginx/sites-available/test.qadi-tech.com /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### 6. Set Permissions
```bash
# Set ownership and permissions
chown -R www-data:www-data /var/www/menu-restaurants
chmod -R 755 /var/www/menu-restaurants
chmod -R 775 /var/www/menu-restaurants/storage /var/www/menu-restaurants/bootstrap/cache
```

### 7. Laravel Optimization
```bash
# Cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE menu_restaurant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Update .env with database credentials
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=menu_restaurant/' .env
sed -i 's/DB_USERNAME=root/DB_USERNAME=your_db_user/' .env
sed -i 's/DB_PASSWORD=/DB_PASSWORD=your_db_password/' .env

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed
```

### 9. Queue Workers (Optional)
```bash
# For background job processing
php artisan queue:work --daemon
```

## Deployment Steps for New Version (V2) branch -> (attendace-web-v1.0.0)

When updating to the new version, please follow these additional steps:

1. **Run the PaymentMethodSeeder:**
   ```bash
   php artisan db:seed PaymentMethodSeeder
   ```

2. **Update Subscription Settings in the Dashboard:**
   - Navigate to the admin dashboard
   - Go to Subscription Settings
   - Configure the new subscription parameters (subscription end date & price & discounts)

3. **Ensure Default Price for School** in Subscription Pricing matches the Default Subscription Price in Settings (create a new Subscription Price)

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

5. **Run the queue worker** (for different queues EX: permits, notifications):
   ```bash
   php artisan queue:work
   ```

## Server Information
- **Domain**: test.qadi-tech.com
- **Server**: Ubuntu 22.04
- **Web Server**: Nginx
- **PHP Version**: 8.2
- **SSL**: Let's Encrypt
- **Document Root**: /var/www/menu-restaurants/public

## Troubleshooting
- Check nginx error logs: `tail -f /var/log/nginx/error.log`
- Check PHP-FPM logs: `tail -f /var/log/php8.2-fpm.log`
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Clear Laravel cache: `php artisan cache:clear`
# dahab-restaurant
