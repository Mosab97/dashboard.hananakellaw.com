# Running Multiple Docker Projects

When you have multiple Laravel projects, you need to avoid conflicts between them.

## Quick Setup for Another Project

### Method 1: Copy Docker Files (Recommended)

1. **Copy these files to your new project:**
   ```bash
   # Navigate to your new project
   cd /path/to/your-new-project
   
   # Copy Docker files from this project
   cp /path/to/this-project/Dockerfile .
   cp /path/to/this-project/docker-compose.yml .
   cp /path/to/this-project/docker-setup.sh .
   cp /path/to/this-project/Makefile .
   cp /path/to/this-project/.dockerignore .
   cp -r /path/to/this-project/docker ./
   ```

2. **Edit `docker-compose.yml` - Change ports and container names:**
   ```yaml
   services:
     app:
       container_name: project2-app  # Change this
       # ... rest stays the same
   
     nginx:
       container_name: project2-nginx  # Change this
       ports:
         - "8001:80"  # Change port (was 8000)
   
     mysql:
       container_name: project2-mysql  # Change this
       ports:
         - "3307:3306"  # Change port (was 3306)
   
     phpmyadmin:
       container_name: project2-phpmyadmin  # Change this
       ports:
         - "8081:80"  # Change port (was 8080)
   
   networks:
     laravel:
       name: project2_network  # Add this line
   
   volumes:
     mysql_data:
       name: project2_mysql_data  # Add this line
   ```

3. **Update `.env` file:**
   ```bash
   APP_URL=http://localhost:8001  # Match your nginx port
   DB_DATABASE=project2_db  # Different database name
   ```

4. **Run setup:**
   ```bash
   ./docker-setup.sh
   ```

---

## Port Assignments Example

Here's how to organize ports for multiple projects:

| Project | App | phpMyAdmin | MySQL |
|---------|-----|------------|-------|
| Project 1 | 8000 | 8080 | 3306 |
| Project 2 | 8001 | 8081 | 3307 |
| Project 3 | 8002 | 8082 | 3308 |
| Project 4 | 8003 | 8083 | 3309 |

---

## Method 2: Quick Start/Stop Projects

If you don't need both projects running simultaneously:

```bash
# Work on Project 1
cd /path/to/project1
docker-compose up -d

# When done, stop it
docker-compose down

# Work on Project 2
cd /path/to/project2
docker-compose up -d
```

This way you can use the same ports (80, 3306, 8080) for all projects since only one runs at a time.

---

## Method 3: Create a Docker Template

Create a reusable template script:

```bash
#!/bin/bash
# setup-docker-for-project.sh

if [ -z "$1" ]; then
    echo "Usage: ./setup-docker-for-project.sh <project-name> <app-port> <phpmyadmin-port> <mysql-port>"
    echo "Example: ./setup-docker-for-project.sh myapp 8001 8081 3307"
    exit 1
fi

PROJECT_NAME=$1
APP_PORT=${2:-8000}
PHPMYADMIN_PORT=${3:-8080}
MYSQL_PORT=${4:-3306}

echo "Setting up Docker for: $PROJECT_NAME"
echo "Ports - App: $APP_PORT, phpMyAdmin: $PHPMYADMIN_PORT, MySQL: $MYSQL_PORT"

# Update docker-compose.yml with new ports and names
sed -i "s/laravel-app/${PROJECT_NAME}-app/g" docker-compose.yml
sed -i "s/laravel-nginx/${PROJECT_NAME}-nginx/g" docker-compose.yml
sed -i "s/laravel-mysql/${PROJECT_NAME}-mysql/g" docker-compose.yml
sed -i "s/laravel-phpmyadmin/${PROJECT_NAME}-phpmyadmin/g" docker-compose.yml
sed -i "s/8000:80/${APP_PORT}:80/" docker-compose.yml
sed -i "s/8080:80/${PHPMYADMIN_PORT}:80/" docker-compose.yml
sed -i "s/3306:3306/${MYSQL_PORT}:3306/" docker-compose.yml

echo "✓ Docker setup completed for $PROJECT_NAME"
echo "Run: docker-compose up -d"
```

---

## Managing Multiple Projects

### See All Running Containers
```bash
docker ps
```

### Stop All Projects
```bash
# Stop all Docker containers
docker stop $(docker ps -q)
```

### Remove Unused Containers/Images
```bash
# Clean up stopped containers
docker container prune

# Clean up unused images
docker image prune

# Clean up everything (careful!)
docker system prune -a
```

### View Resource Usage
```bash
docker stats
```

---

## Best Practices

### 1. **Use Different Ports**
Always assign unique ports to avoid conflicts:
- Project 1: 8000, 8080, 3306
- Project 2: 8001, 8081, 3307
- etc.

### 2. **Use Unique Container Names**
```yaml
container_name: projectname-app
```

### 3. **Use Unique Network Names**
```yaml
networks:
  laravel:
    name: projectname_network
```

### 4. **Use Unique Volume Names**
```yaml
volumes:
  mysql_data:
    name: projectname_mysql_data
```

### 5. **Document Your Ports**
Keep a file like `PORTS.md` in each project:
```markdown
# Project Ports
- App: http://localhost:8001
- phpMyAdmin: http://localhost:8081
- MySQL: localhost:3307
```

### 6. **Use Docker Compose Profiles (Advanced)**
You can also use Docker Compose profiles to run multiple projects with the same compose file:
```yaml
services:
  app:
    profiles: ["project1"]
    # ...
```

---

## Troubleshooting Multiple Projects

### Port Already in Use
```bash
# Find what's using a port (macOS/Linux)
lsof -i :8000

# Kill the process
kill -9 <PID>

# Or just change the port in docker-compose.yml
```

### Container Name Conflicts
```bash
# Remove container
docker rm -f container-name

# Or rename in docker-compose.yml
```

### Database Conflicts
Make sure each project uses a different database name in `.env`:
```env
DB_DATABASE=project1_db
```

---

## Quick Reference

### Project 1 (Current)
```bash
cd /path/to/project1
make up      # or: docker-compose up -d
# http://localhost:8000
```

### Project 2 (New)
```bash
cd /path/to/project2
# 1. Copy Docker files
# 2. Edit docker-compose.yml (change ports & names)
# 3. Run: docker-compose up -d
# http://localhost:8001
```

---

## Example: Full Setup for Second Project

```bash
# 1. Go to your new project
cd /path/to/project2

# 2. Copy Docker files from project1
cp -r /path/to/project1/{Dockerfile,docker-compose.yml,docker-setup.sh,Makefile,.dockerignore,docker} .

# 3. Edit docker-compose.yml - replace all occurrences:
#    - laravel-app -> project2-app
#    - laravel-nginx -> project2-nginx
#    - laravel-mysql -> project2-mysql
#    - laravel-phpmyadmin -> project2-phpmyadmin
#    - "8000:80" -> "8001:80"
#    - "8080:80" -> "8081:80"
#    - "3306:3306" -> "3307:3306"

# 4. Run setup
./docker-setup.sh

# 5. Access at http://localhost:8001
```

---

## Need Help?

Run `make help` in any project to see available commands.

Each project is independent, so you can start/stop them individually! 🚀

