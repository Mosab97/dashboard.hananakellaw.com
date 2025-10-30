# Docker Quick Start Guide

## 🚀 Get Started in 2 Steps

### 1. Run the Setup Script
```bash
./docker-setup.sh
```

This will automatically:
- Create `.env` file with Docker configuration
- Build and start all Docker containers
- Generate application key
- Run database migrations
- Set proper permissions

### 2. Access the Application
- **Application**: http://localhost
- **phpMyAdmin**: http://localhost:8080

### 3. Done! 🎉

---

## 📋 Quick Commands Reference

### Using Make (Recommended)
```bash
make install        # Initial setup (first time only)
make up             # Start containers
make down           # Stop containers
make restart        # Restart all containers
make logs           # View all logs
make shell          # Access container shell
make phpmyadmin     # Open phpMyAdmin in browser
make migrate        # Run migrations
make fresh          # Fresh database with seeders
make cache-clear    # Clear all caches
make help           # See all available commands
```

### Using Docker Compose Directly
```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# View logs
docker-compose logs -f

# Run artisan commands
docker-compose exec app php artisan migrate

# Access shell
docker-compose exec app bash
```

---

## 🔧 Common Tasks

### Database Operations
```bash
# Run migrations
make migrate
# or
docker-compose exec app php artisan migrate

# Seed database
make seed
# or
docker-compose exec app php artisan db:seed

# Fresh migration with seeding
make fresh
# or
docker-compose exec app php artisan migrate:fresh --seed
```

### Access phpMyAdmin
- **URL**: http://localhost:8080
- **Server**: mysql
- **Username**: laravel
- **Password**: secret

Or use:
```bash
make phpmyadmin
```

### Clear Caches
```bash
# Clear all caches
make cache-clear

# Individual cache clears
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### View Logs
```bash
# All logs
make logs

# Specific service logs
make logs-app
make logs-nginx
make logs-mysql

# Or with docker-compose
docker-compose logs -f app
```

### Access Database
```bash
# MySQL CLI
make shell-mysql
# Password: secret (default)

# Or from host machine
mysql -h 127.0.0.1 -P 3306 -u laravel -p

# Or use phpMyAdmin at http://localhost:8080
```

---

## 🐛 Troubleshooting

### Containers won't start?
```bash
# Check container status
docker-compose ps

# View error logs
docker-compose logs

# Start fresh
make clean
make install
```

### Permission errors?
```bash
make permissions
# or
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Database connection issues?
Check your `.env` file:
```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### Port already in use?
Change ports in `docker-compose.yml`:
```yaml
nginx:
  ports:
    - "8000:80"  # Use port 8000 instead of 80

phpmyadmin:
  ports:
    - "8081:80"  # Use port 8081 instead of 8080
```

---

## 📦 What's Included?

The Docker setup includes:
- **PHP 8.3** with all required extensions
- **Nginx** web server (port 80)
- **MySQL 8.0** database (port 3306)
- **phpMyAdmin** for database management (port 8080)

---

## 🔧 Services

| Service | URL/Port | Credentials |
|---------|----------|-------------|
| **Application** | http://localhost | - |
| **phpMyAdmin** | http://localhost:8080 | laravel / secret |
| **MySQL** | localhost:3306 | laravel / secret |

---

## ⚡ Pro Tips

1. **Use `make help`** to see all available commands
2. **Use phpMyAdmin** at http://localhost:8080 for easy database management
3. **Keep containers running** - they use minimal resources when idle
4. **Use `.env` for configuration** - never commit secrets to git
5. **Check logs regularly** with `make logs` to catch issues early

---

## 🆘 Need Help?

1. Check the logs: `make logs` or `docker-compose logs -f`
2. Check container status: `docker-compose ps`
3. Restart containers: `make restart`
4. Access phpMyAdmin: http://localhost:8080

---

**Happy Coding! 🚀**
