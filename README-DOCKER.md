# Docker Setup for Laravel Application

Simple Docker setup for running your Laravel application with MySQL and phpMyAdmin.

## Prerequisites

- Docker (version 20.10 or higher)
- Docker Compose (version 2.0 or higher)

## Quick Start

### 1. Run the Setup Script

```bash
./docker-setup.sh
```

This will automatically:
- Create `.env` file if it doesn't exist
- Build and start all containers
- Generate application key
- Install composer dependencies
- Run database migrations
- Set proper permissions

### 2. Access the Application

- **Application**: http://localhost
- **phpMyAdmin**: http://localhost:8080

## Services

### Application (PHP-FPM + Nginx)
- **URL**: http://localhost
- **PHP Version**: 8.3
- **Web Server**: Nginx

### MySQL Database
- **Host**: localhost (or `mysql` from inside containers)
- **Port**: 3306
- **Database**: laravel
- **Username**: laravel
- **Password**: secret
- **Root Password**: root

### phpMyAdmin
- **URL**: http://localhost:8080
- **Server**: mysql
- **Username**: laravel
- **Password**: secret

## Common Commands

### Container Management

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Restart containers
docker-compose restart

# View logs
docker-compose logs -f

# View logs for specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# Check container status
docker-compose ps
```

### Application Commands

```bash
# Access container shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear

# Run composer commands
docker-compose exec app composer install
docker-compose exec app composer update
```

### Database Management

#### Using phpMyAdmin (Easy Way)
Visit http://localhost:8080 and login with:
- Server: mysql
- Username: laravel
- Password: secret

#### Using MySQL CLI
```bash
# Access MySQL from container
docker-compose exec mysql mysql -u laravel -p
# Password: secret

# Access MySQL from host machine
mysql -h 127.0.0.1 -P 3306 -u laravel -p

# Import database dump
docker-compose exec -T mysql mysql -u laravel -p laravel < dump.sql

# Export database
docker-compose exec mysql mysqldump -u laravel -p laravel > dump.sql
```

## Using Make Commands

For convenience, you can use the included Makefile:

```bash
# Initial setup
make install

# Container management
make up              # Start containers
make down            # Stop containers
make restart         # Restart containers
make logs            # View all logs
make ps              # Show container status

# Application
make shell           # Access container shell
make artisan cmd="migrate"    # Run artisan command
make composer cmd="install"   # Run composer command

# Database
make migrate         # Run migrations
make seed            # Seed database
make fresh           # Fresh migration + seed
make shell-mysql     # Access MySQL CLI
make phpmyadmin      # Open phpMyAdmin in browser

# Maintenance
make cache-clear     # Clear all caches
make permissions     # Fix permissions
make clean           # Remove all containers/volumes
make rebuild         # Clean rebuild

# Help
make help            # Show all available commands
```

## Environment Configuration

The `.env` file is created automatically with these settings:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

You can customize these values by editing the `.env` file.

## Troubleshooting

### Permission Issues

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues

1. Make sure MySQL container is healthy:
```bash
docker-compose ps
```

2. Check MySQL logs:
```bash
docker-compose logs mysql
```

3. Verify `.env` settings match the database credentials

### Port Conflicts

If port 80 or 8080 is already in use, edit `docker-compose.yml`:

```yaml
nginx:
  ports:
    - "8000:80"  # Change to available port

phpmyadmin:
  ports:
    - "8081:80"  # Change to available port
```

### Start Fresh

To completely reset everything:

```bash
# Stop and remove everything
docker-compose down -v
docker-compose rm -f

# Remove images
docker rmi laravel-app

# Start fresh
./docker-setup.sh
```

## Project Structure

```
.
├── Dockerfile              # PHP application container
├── docker-compose.yml      # Docker services configuration
├── docker-setup.sh        # Automated setup script
├── Makefile               # Convenient make commands
└── docker/
    ├── nginx/
    │   └── default.conf   # Nginx configuration
    ├── php/
    │   └── php.ini        # PHP settings
    └── mysql/
        └── my.cnf         # MySQL settings
```

## Production Considerations

For production deployment:

1. **Change all default passwords** in `.env` and `docker-compose.yml`
2. **Set `APP_DEBUG=false`** and `APP_ENV=production`
3. **Remove phpMyAdmin** (comment it out in `docker-compose.yml`)
4. **Use proper SSL certificates** for HTTPS
5. **Set up proper backup strategies** for database
6. **Use external volumes** for persistent data
7. **Implement proper security measures**

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)

## Support

For issues, check the logs:

```bash
docker-compose logs -f
```

Or access the container shell for debugging:

```bash
docker-compose exec app bash
```
