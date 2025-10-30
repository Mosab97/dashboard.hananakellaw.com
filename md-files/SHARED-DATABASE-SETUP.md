# Use One Shared MySQL/phpMyAdmin For Multiple Laravel Projects

This guide shows how to run a single MySQL (and one phpMyAdmin) for many Laravel projects. Each project keeps its own codebase, Composer/NPM packages, and `.env`, but they all connect to the same database server with a unique database name.

---

## Overview

- One shared stack you run once: `shared-mysql` + `shared-phpmyadmin`
- Each project:
  - Has its own Docker `app` + `nginx` only (no per-project MySQL/phpMyAdmin)
  - Uses unique DB name/user in its own `.env`
  - Joins the same external Docker network to reach the shared DB

---

## 1) Create the Shared Database Stack (run once)

Create a folder for the shared DB stack, for example: `~/docker-shared-db`.

Inside it, create `docker-compose.yml` with:

```yaml
services:
  shared-mysql:
    image: mysql:8.0
    container_name: shared-mysql
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3306:3306"              # Host port; change if you already use 3306
    volumes:
      - shared_mysql_data:/var/lib/mysql
    networks:
      - shared

  shared-phpmyadmin:
    image: phpmyadmin:latest
    container_name: shared-phpmyadmin
    restart: unless-stopped
    environment:
      PMA_HOST: shared-mysql
      PMA_PORT: 3306
    ports:
      - "8080:80"               # Host port for phpMyAdmin
    networks:
      - shared

networks:
  shared:
    name: shared_network

volumes:
  shared_mysql_data:
    name: shared_mysql_data
```

Start it:

```bash
cd ~/docker-shared-db
docker-compose up -d
```

Access phpMyAdmin at: http://localhost:8080 (server: `shared-mysql`, user: `root`, pass: `root`).

---

## 2) Prepare Each Project To Use The Shared DB

In each Laravel project, you will:

1. Remove per-project MySQL/phpMyAdmin services (keep only `app` and `nginx`).
2. Attach the compose file to the external `shared_network`.
3. Set DB env variables to point to `shared-mysql`.

Example minimal changes to your project `docker-compose.yml`:

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: project1-app
    container_name: project1-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/local.ini
    environment:
      - DB_HOST=shared-mysql        # <— Point to the shared DB service name
      - DB_PORT=3306
      - DB_DATABASE=project1_db     # <— Unique DB per project
      - DB_USERNAME=root            # (or a dedicated user, see Step 3)
      - DB_PASSWORD=root
    networks:
      - shared
    depends_on: []                  # Remove mysql/phpmyadmin dependencies

  nginx:
    image: nginx:alpine
    container_name: project1-nginx
    restart: unless-stopped
    ports:
      - "8000:80"                  # per-project app port
    volumes:
      - ./:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - shared

networks:
  shared:
    external: true
    name: shared_network
```

In the same project, set your `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=shared-mysql
DB_PORT=3306
DB_DATABASE=project1_db
DB_USERNAME=root
DB_PASSWORD=root

# Recommended for no-Redis setup
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

Bring the project up as usual:

```bash
docker-compose up -d --build
```

Your app: http://localhost:8000  
phpMyAdmin (shared): http://localhost:8080

---

## 3) (Optional) Create Per-Project MySQL Users

For better isolation, create a dedicated MySQL user and database per project. In phpMyAdmin (or MySQL CLI):

```sql
-- Replace names/passwords as needed
CREATE DATABASE IF NOT EXISTS project1_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'project1_user'@'%' IDENTIFIED BY 'project1_pass';
GRANT ALL PRIVILEGES ON project1_db.* TO 'project1_user'@'%';
FLUSH PRIVILEGES;
```

Then update the project `.env`:

```env
DB_DATABASE=project1_db
DB_USERNAME=project1_user
DB_PASSWORD=project1_pass
```

Run migrations from the project container:

```bash
docker-compose exec app php artisan migrate
```

---

## 4) Naming & Ports Convention

- Give each project a unique app port: 8000, 8001, 8002, ...
- Use unique DB names: `project1_db`, `project2_db`, ...
- Optionally use unique MySQL users per project.
- All projects connect to host `shared-mysql` on network `shared_network`.

Example table:

| Project | App URL | DB Name | DB User |
|---------|---------|---------|---------|
| Project 1 | http://localhost:8000 | project1_db | project1_user |
| Project 2 | http://localhost:8001 | project2_db | project2_user |
| Project 3 | http://localhost:8002 | project3_db | project3_user |

---

## 5) Troubleshooting

- Port 3306 already in use on host: change to `"3307:3306"` in shared stack and use 3307 from host. Containers still use 3306 to reach `shared-mysql` via Docker network.
- Project cannot reach DB: ensure project is attached to `shared_network` and `DB_HOST=shared-mysql`.
- Authentication errors: verify user/password and privileges for the specific database.
- Migrations failing to create DB: pre-create the database in phpMyAdmin, then rerun migrations.

---

## 6) Summary

- Run shared DB stack once (MySQL + phpMyAdmin) on `shared_network`.
- Each project keeps its own code, dependencies, and `.env`.
- Each project connects to `shared-mysql` with a unique database name (and optionally its own user).
- Result: less resource usage, centralized DB admin, clean separation per project.


