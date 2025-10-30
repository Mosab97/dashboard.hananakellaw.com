#!/bin/bash

# Fix Redis Error - Update .env to use file-based storage

echo "Fixing Redis configuration in .env file..."

# Update cache driver from redis to file
sed -i.bak 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env

# Update session driver from redis to file
sed -i.bak 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env

# Update queue connection from redis to sync
sed -i.bak 's/QUEUE_CONNECTION=redis/QUEUE_CONNECTION=sync/' .env

# Comment out Redis settings if they exist
sed -i.bak 's/^REDIS_HOST=/# REDIS_HOST=/' .env
sed -i.bak 's/^REDIS_PASSWORD=/# REDIS_PASSWORD=/' .env
sed -i.bak 's/^REDIS_PORT=/# REDIS_PORT=/' .env

echo "✓ .env file updated"
echo ""
echo "Clearing Laravel cache..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

echo ""
echo "✓ Done! Try accessing your application again."
echo "  → http://localhost:8000"

