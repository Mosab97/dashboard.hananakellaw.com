#!/bin/bash

# Simple Docker Setup Script for Laravel Application

set -e

echo "=========================================="
echo "Laravel Docker Setup"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

print_success "Docker and Docker Compose are installed"

# Check if .env file exists
if [ ! -f .env ]; then
    print_warning ".env file not found"
    echo "Creating .env file from environment template..."
    
    cat > .env << 'EOF'
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
DB_ROOT_PASSWORD=root

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF
    
    print_success ".env file created"
else
    print_success ".env file already exists"
fi

# Build and start Docker containers
echo ""
echo "Building Docker containers..."
docker-compose up -d --build

echo ""
echo "Waiting for containers to be ready..."
sleep 10

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo ""
    echo "Generating application key..."
    docker-compose exec -T app php artisan key:generate --no-interaction
    print_success "Application key generated"
fi

# Install composer dependencies
echo ""
echo "Installing Composer dependencies..."
docker-compose exec -T app composer install --no-interaction --optimize-autoloader
print_success "Composer dependencies installed"

# Set permissions
echo ""
echo "Setting proper permissions..."
docker-compose exec -T app chmod -R 775 storage bootstrap/cache 2>/dev/null || true
print_success "Permissions set"

# Run migrations
echo ""
echo "Running database migrations..."
docker-compose exec -T app php artisan migrate --force
print_success "Database migrations completed"

# Create storage link
echo ""
echo "Creating storage link..."
docker-compose exec -T app php artisan storage:link 2>/dev/null || true
print_success "Storage link created"

# Clear configuration cache
echo ""
echo "Clearing configuration cache..."
docker-compose exec -T app php artisan config:clear
print_success "Configuration cache cleared"

echo ""
echo "=========================================="
print_success "Setup completed successfully!"
echo "=========================================="
echo ""
echo "Your application is now running at:"
echo "  - Application: http://localhost:8000"
echo "  - phpMyAdmin: http://localhost:8080"
echo ""
echo "Database credentials:"
echo "  - Host: localhost (or mysql from inside containers)"
echo "  - Port: 3306"
echo "  - Database: laravel"
echo "  - Username: laravel"
echo "  - Password: secret"
echo "  - Root Password: root"
echo ""
echo "Useful commands:"
echo "  - View logs: docker-compose logs -f"
echo "  - Stop containers: docker-compose down"
echo "  - Access shell: docker-compose exec app bash"
echo "  - Run artisan: docker-compose exec app php artisan [command]"
echo ""
