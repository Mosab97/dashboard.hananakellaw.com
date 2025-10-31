# Dashboard Hananakellaw - Laravel Sail Setup Guide

This guide explains how to run the Dashboard Hananakellaw project using Laravel Sail with migrations and seeders.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start (For Already Configured Projects)](#quick-start-for-already-configured-projects)
- [Complete Setup Commands (Quick Reference)](#complete-setup-commands-quick-reference)
- [Initial Setup (First Time Only)](#initial-setup-first-time-only)
- [Database Setup](#database-setup)
- [Database Connection Info](#database-connection-info)
- [Verify Installation](#verify-installation)
- [Common Sail Commands](#common-sail-commands)
- [Accessing the Application](#accessing-the-application)
- [Troubleshooting](#troubleshooting)
- [Setup Summary](#setup-summary)
- [Project Ports](#project-ports)

---

## Prerequisites

- Docker Desktop installed and running
- Git (for cloning the repository)

## Quick Start (For Already Configured Projects)

If the project is already configured with `.env` file and dependencies installed:

```bash
# Start containers
./vendor/bin/sail up -d

# Access the application
open http://localhost:8001
```

To stop:
```bash
./vendor/bin/sail down
```

---

## Complete Setup Commands (Quick Reference)

For a quick copy-paste setup, run these commands in sequence:

```bash
# 1. Install dependencies (if vendor doesn't exist)
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php81-composer:latest composer install --ignore-platform-reqs

# 2. Copy environment file
cp .env.example .env

# 3. Update .env with these values:
# DB_HOST=mysql
# DB_DATABASE=hananakellaw_db
# DB_USERNAME=sail
# DB_PASSWORD=password
# APP_PORT=8001
# FORWARD_DB_PORT=3309
# FORWARD_REDIS_PORT=6380
# VITE_PORT=5174

# 4. Install Sail
php artisan sail:install --with=mysql,redis

# 5. Start containers
./vendor/bin/sail up -d

# 6. Generate app key
./vendor/bin/sail artisan key:generate

# 7. Create database
docker exec dashboardhananakellawcom-mysql-1 mysql -u root -ppassword -e "CREATE DATABASE IF NOT EXISTS hananakellaw_db; GRANT ALL PRIVILEGES ON hananakellaw_db.* TO 'sail'@'%'; FLUSH PRIVILEGES;"

# 8. Run migrations
./vendor/bin/sail artisan migrate

# 9. Run seeders
./vendor/bin/sail artisan db:seed

# 10. Access application
open http://localhost:8001

# 11. Access phpMyAdmin (optional but recommended)
open http://localhost:8080
```

---

## Initial Setup (First Time Only)

### 1. Install Dependencies

If you don't have PHP installed locally, use Docker to install composer dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

### 2. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Update the following variables in `.env`:

```env
APP_URL=http://localhost:8001
APP_PORT=8001

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=hananakellaw_db
DB_USERNAME=sail
DB_PASSWORD=password

FORWARD_DB_PORT=3309
FORWARD_REDIS_PORT=6380
VITE_PORT=5174
FORWARD_PHPMYADMIN_PORT=8080
```

> **Note:** Using custom ports to avoid conflicts with other projects:
> - `APP_PORT=8001` (instead of 8000 used by HayatCRM)
> - `FORWARD_DB_PORT=3309` (instead of 3308 used by HayatCRM)
> - `FORWARD_REDIS_PORT=6380` (instead of default 6379)
> - `VITE_PORT=5174` (instead of default 5173)
> - `FORWARD_PHPMYADMIN_PORT=8080` (phpMyAdmin web interface)

### 3. Install Laravel Sail

Publish Sail configuration with MySQL and Redis:

```bash
php artisan sail:install --with=mysql,redis
```

### 4. Start Sail Containers

```bash
./vendor/bin/sail up -d
```

This will start:
- Laravel application (port 8001)
- MySQL database (port 3309)
- Redis cache (port 6380)
- Vite dev server (port 5174)
- phpMyAdmin (port 8080)

> **Note:** If you encounter "port already in use" errors during first startup:
> 1. Stop containers: `./vendor/bin/sail down`
> 2. Verify ports in `.env` file are set correctly (see step 2)
> 3. Change conflicting ports if needed
> 4. Restart: `./vendor/bin/sail up -d`

**Common Port Conflicts During Setup:**
- **Redis (6379)**: Add `FORWARD_REDIS_PORT=6380` to `.env`
- **Vite (5173)**: Add `VITE_PORT=5174` to `.env`  
- If ports 6380 or 5174 are also taken, increment to 6381, 5175, etc.

### 5. Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```

## Database Setup

### 1. Create Database

```bash
docker exec dashboardhananakellawcom-mysql-1 mysql -u root -ppassword -e "CREATE DATABASE IF NOT EXISTS hananakellaw_db; GRANT ALL PRIVILEGES ON hananakellaw_db.* TO 'sail'@'%'; FLUSH PRIVILEGES;"
```

> **Note:** The container name is `dashboardhananakellawcom-mysql-1` (dots and hyphens removed). You can verify with `docker ps` command.

### 2. Run Migrations

Create all database tables:

```bash
./vendor/bin/sail artisan migrate
```

### 3. Run Seeders

Populate the database with initial data:

```bash
./vendor/bin/sail artisan db:seed
```

This will run the following seeders:
- **SettingsSeeder** - Application settings
- **ConstantsTableSeederV3** - System constants
- **RolesAndPermissionsSeeder** - User roles and permissions
- **AdminSeeder** - Admin user account
- **MenuSeeder** - Navigation menus
- **AppointmentTypeSeeder** - Appointment types
- **ArticleTypeSeeder** - Article types

#### Run Individual Seeders (Optional)

If you need to run specific seeders:

```bash
# Run a specific seeder
./vendor/bin/sail artisan db:seed --class=AdminSeeder

# Run all seeders again (will duplicate data if not handled properly)
./vendor/bin/sail artisan db:seed
```

### 4. Fresh Migration (Optional)

To drop all tables and rebuild from scratch:

```bash
# Drop all tables, run migrations, and seed
./vendor/bin/sail artisan migrate:fresh --seed
```

> ⚠️ **Warning:** This will delete all data in your database!

## Database Connection Info

### Using phpMyAdmin (Recommended)

Access phpMyAdmin through your browser at:

**http://localhost:8080**

Login credentials:
- **Username**: `sail`
- **Password**: `password`
- **Server**: `mysql` (already pre-configured)

> **Note:** phpMyAdmin is already configured to connect to your MySQL database. Just click "Go" or "Login" after opening the URL.

### Using Database Clients (Alternative)

Connect to the database from your local machine using tools like TablePlus, MySQL Workbench, DBeaver, etc:

- **Host**: `127.0.0.1`
- **Port**: `3309`
- **Username**: `sail`
- **Password**: `password`
- **Database**: `hananakellaw_db`

## Verify Installation

Check that all containers are running:

```bash
docker ps | grep dashboardhananakellawcom
```

You should see four containers:
- `dashboardhananakellawcom-laravel.test-1` (Application)
- `dashboardhananakellawcom-mysql-1` (Database)
- `dashboardhananakellawcom-redis-1` (Cache)
- `dashboardhananakellawcom-phpmyadmin-1` (phpMyAdmin)

Test the application:
```bash
curl -I http://localhost:8001
```

You should receive a response with HTTP status code (200 or 302).

## Common Sail Commands

### Container Management

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Restart containers
./vendor/bin/sail restart

# View logs
./vendor/bin/sail logs -f
```

### Artisan Commands

```bash
# Run any artisan command
./vendor/bin/sail artisan [command]

# Examples:
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:list
```

### Migration Commands

```bash
# Run pending migrations
./vendor/bin/sail artisan migrate

# Rollback last migration
./vendor/bin/sail artisan migrate:rollback

# Rollback all migrations
./vendor/bin/sail artisan migrate:reset

# Fresh migration (drop all tables and rebuild)
./vendor/bin/sail artisan migrate:fresh

# Fresh migration with seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Check migration status
./vendor/bin/sail artisan migrate:status
```

### Composer Commands

```bash
./vendor/bin/sail composer install
./vendor/bin/sail composer update
./vendor/bin/sail composer require package-name
```

### NPM Commands

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build
```

### Database Commands

```bash
# Access MySQL CLI
./vendor/bin/sail mysql

# Access MySQL as root
docker exec -it dashboardhananakellawcom-mysql-1 mysql -u root -ppassword

# Export database
docker exec dashboardhananakellawcom-mysql-1 mysqldump -u sail -ppassword hananakellaw_db > backup.sql

# Import database backup
docker exec -i dashboardhananakellawcom-mysql-1 mysql -u sail -ppassword hananakellaw_db < backup.sql
```

### phpMyAdmin Management

phpMyAdmin provides a web-based interface for managing your MySQL database:

**Access URL**: http://localhost:8080

**Features:**
- Visual table browser
- SQL query editor with syntax highlighting
- Database structure management
- Import/Export functionality (SQL, CSV, Excel, etc.)
- User and permissions management
- Database search and replace
- Query bookmarks

**Common Tasks in phpMyAdmin:**

1. **View Tables**: 
   - Click on `hananakellaw_db` database in left sidebar
   - Browse all tables and their data

2. **Run SQL Queries**:
   - Click on "SQL" tab
   - Enter your query and click "Go"

3. **Export Database**:
   - Select `hananakellaw_db` database
   - Click "Export" tab
   - Choose format (SQL recommended for backups)
   - Click "Go" to download

4. **Import Database**:
   - Select `hananakellaw_db` database
   - Click "Import" tab
   - Choose file and click "Go"

5. **Browse/Edit Data**:
   - Click on any table name
   - Click "Edit" icon next to any row
   - Make changes and click "Go" to save

### Container Shell Access

```bash
# Access application container
./vendor/bin/sail shell

# Access as root
./vendor/bin/sail root-shell
```

## Shell Alias (Optional)

Add this to your `~/.zshrc` or `~/.bashrc` for easier usage:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Then reload your shell:

```bash
source ~/.zshrc  # or source ~/.bashrc
```

Now you can use shorter commands:

```bash
sail up -d
sail artisan migrate
sail composer install
```

## Accessing the Application

Once the containers are running, access the application at:

**http://localhost:8001**

### Access phpMyAdmin

Access phpMyAdmin to manage your database:

**http://localhost:8080**

Login with:
- **Username**: `sail`
- **Password**: `password`

From phpMyAdmin, you can:
- View all database tables
- Browse and edit data
- Run SQL queries
- Export/import databases
- Manage database structure

### Default Admin Credentials

After running the seeders, you can login with either of these accounts:

**Admin Account:**
- **Email**: `admin@gmail.com`
- **Password**: `admin`

**Alternative Admin Account:**
- **Email**: `hossam@gmail.com`
- **Password**: `admin`

Both accounts have super-admin privileges.

## Troubleshooting

### Port Already in Use

If you encounter "port already in use" errors:

1. **For MySQL port conflict**: Change `FORWARD_DB_PORT` in `.env` to a different port (e.g., 3310, 3311)
2. **For Application port conflict**: Change `APP_PORT` in `.env` (e.g., 8002, 8003)
3. **For Redis port conflict**: Add or change `FORWARD_REDIS_PORT` in `.env` (e.g., 6381, 6382)
4. **For Vite port conflict**: Add or change `VITE_PORT` in `.env` (e.g., 5175, 5176)
5. Stop containers: `./vendor/bin/sail down`
6. Start containers again: `./vendor/bin/sail up -d`

**Common Port Conflicts:**
- Port 6379 (Redis) - often used by other projects
- Port 5173 (Vite) - often used by other projects
- Port 8000 (App) - often used by other projects
- Port 3306/3308 (MySQL) - often used by other projects

### MySQL Connection Issues

If you can't connect to MySQL:

1. Ensure containers are running: `docker ps`
2. Check container name: `docker ps | grep mysql`
3. Verify permissions: `docker exec dashboardhananakellawcom-mysql-1 mysql -u root -ppassword -e "SHOW GRANTS FOR 'sail'@'%';"`
4. Check database exists: `docker exec dashboardhananakellawcom-mysql-1 mysql -u root -ppassword -e "SHOW DATABASES;"`

### Migration Errors

If migrations fail:

1. Check if the database exists and is accessible
2. Review the error message carefully
3. Try running migrations one by one to identify the problematic migration
4. Clear cache: `./vendor/bin/sail artisan config:clear`

### Seeder Errors

If seeders fail:

1. Ensure migrations have run successfully first
2. Check the specific seeder file for errors
3. Try running seeders individually to identify the problem
4. Check for duplicate data issues if re-running seeders

### Clear Application Cache

```bash
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan optimize:clear
```

### Container Issues

```bash
# Stop and remove all containers
./vendor/bin/sail down

# Rebuild containers
./vendor/bin/sail build --no-cache

# Start fresh
./vendor/bin/sail up -d
```

### MYSQL_EXTRA_OPTIONS Warning

If you see this warning when running sail commands:
```
The "MYSQL_EXTRA_OPTIONS" variable is not set. Defaulting to a blank string.
```

This is a harmless warning and can be safely ignored. The MySQL container will work correctly without this variable. If you want to suppress it, add this to your `.env`:

```env
MYSQL_EXTRA_OPTIONS=
```

### Container Names

The project creates containers with the following naming pattern:
- Application: `dashboardhananakellawcom-laravel.test-1`
- MySQL: `dashboardhananakellawcom-mysql-1`
- Redis: `dashboardhananakellawcom-redis-1`

> **Note:** Docker Compose automatically converts dots (`.`) and hyphens (`-`) in directory names to create container names. The directory `dashboard.hananakellaw.com` becomes `dashboardhananakellawcom` in container names.

## Stopping the Project

To stop all containers:

```bash
./vendor/bin/sail down
```

To stop and remove volumes (⚠️ this will delete your database):

```bash
./vendor/bin/sail down -v
```

## Development Workflow

### After Pulling New Code

```bash
# Update dependencies
./vendor/bin/sail composer install
./vendor/bin/sail npm install

# Run new migrations
./vendor/bin/sail artisan migrate

# Clear caches
./vendor/bin/sail artisan optimize:clear
```

### Creating New Migrations

```bash
# Create a new migration
./vendor/bin/sail artisan make:migration create_example_table

# Create a model with migration
./vendor/bin/sail artisan make:model Example -m
```

### Creating New Seeders

```bash
# Create a new seeder
./vendor/bin/sail artisan make:seeder ExampleSeeder
```

## Available Seeders

The project includes the following seeders:

- `DatabaseSeeder` - Main seeder that calls all essential seeders
- `SettingsSeeder` - Application settings
- `ConstantsTableSeederV3` - System constants
- `RolesAndPermissionsSeeder` - User roles and permissions
- `AdminSeeder` - Admin user account
- `MenuSeeder` - Navigation menus
- `AppointmentTypeSeeder` - Appointment types
- `ArticleTypeSeeder` - Article types
- `CategorySeeder` - Categories
- `SliderSeeder` - Homepage sliders
- `VideoSeeder` - Videos
- `SucessStorySeeder` - Success stories
- `WhyChooseUsSeeder` - Why choose us section
- `CustomerRateSeeder` - Customer ratings
- `HowWeWorkSeeder` - How we work section
- `ArticleSeeder` - Articles
- `ArticleContentSeeder` - Article contents

## Setup Summary

After completing the setup, you will have:

✅ **Running Services:**
- Laravel Application on `http://localhost:8001`
- phpMyAdmin on `http://localhost:8080`
- MySQL Database on port `3309`
- Redis Cache on port `6380`
- Vite Dev Server on port `5174`

✅ **Database:**
- Database: `hananakellaw_db`
- 34 migrations executed
- All essential data seeded

✅ **Admin Accounts:**
- `admin@gmail.com` / `admin`
- `hossam@gmail.com` / `admin`

✅ **Container Management:**
```bash
./vendor/bin/sail up -d    # Start
./vendor/bin/sail down      # Stop
./vendor/bin/sail logs -f   # View logs
./vendor/bin/sail shell     # Access shell
```

## Project Ports

This project uses the following ports (different from other projects to avoid conflicts):

| Service | Port | Alternative Projects |
|---------|------|---------------------|
| Application | 8001 | HayatCRM uses 8000 |
| phpMyAdmin | 8080 | Web interface for MySQL |
| MySQL | 3309 | HayatCRM uses 3308 |
| Redis | 6380 | Default is 6379 |
| Vite | 5174 | Default is 5173 |

## Additional Resources

- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Docker Documentation](https://docs.docker.com/)
- [Laravel Migration Documentation](https://laravel.com/docs/migrations)
- [Laravel Seeding Documentation](https://laravel.com/docs/seeding)

---

## Notes

- This setup guide was created and tested on macOS with Docker Desktop
- All steps have been verified to work with Laravel 10 and PHP 8.1+
- The guide includes solutions to common port conflicts encountered during setup
- For production deployment, additional security configurations are required

