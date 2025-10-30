#!/bin/bash

# Script to copy Docker setup to another project

echo "=========================================="
echo "Copy Docker Setup to New Project"
echo "=========================================="
echo ""

# Check if target path is provided
if [ -z "$1" ]; then
    echo "Usage: ./copy-docker-to-project.sh <target-project-path> [app-port] [phpmyadmin-port] [mysql-port]"
    echo ""
    echo "Example:"
    echo "  ./copy-docker-to-project.sh ../my-other-project 8001 8081 3307"
    echo ""
    exit 1
fi

TARGET_PATH=$1
APP_PORT=${2:-8001}
PHPMYADMIN_PORT=${3:-8081}
MYSQL_PORT=${4:-3307}

# Check if target directory exists
if [ ! -d "$TARGET_PATH" ]; then
    echo "❌ Error: Directory $TARGET_PATH does not exist"
    exit 1
fi

echo "Target Project: $TARGET_PATH"
echo "Ports:"
echo "  - App (Nginx): $APP_PORT"
echo "  - phpMyAdmin: $PHPMYADMIN_PORT"
echo "  - MySQL: $MYSQL_PORT"
echo ""

# Get project name from path
PROJECT_NAME=$(basename "$TARGET_PATH" | tr '[:upper:]' '[:lower:]' | tr '.' '-' | tr '_' '-')

echo "Project Name: $PROJECT_NAME"
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Cancelled."
    exit 1
fi

# Copy files
echo "Copying Docker files..."
cp Dockerfile "$TARGET_PATH/"
cp docker-compose.yml "$TARGET_PATH/"
cp docker-setup.sh "$TARGET_PATH/"
cp Makefile "$TARGET_PATH/"
cp .dockerignore "$TARGET_PATH/"
cp -r docker "$TARGET_PATH/"

echo "✓ Files copied"

# Update docker-compose.yml
echo "Updating docker-compose.yml..."
cd "$TARGET_PATH"

# macOS uses different sed syntax
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s/container_name: laravel-app/container_name: ${PROJECT_NAME}-app/" docker-compose.yml
    sed -i '' "s/container_name: laravel-nginx/container_name: ${PROJECT_NAME}-nginx/" docker-compose.yml
    sed -i '' "s/container_name: laravel-mysql/container_name: ${PROJECT_NAME}-mysql/" docker-compose.yml
    sed -i '' "s/container_name: laravel-phpmyadmin/container_name: ${PROJECT_NAME}-phpmyadmin/" docker-compose.yml
    sed -i '' "s/\"8000:80\"/\"${APP_PORT}:80\"/" docker-compose.yml
    sed -i '' "s/\"8080:80\"/\"${PHPMYADMIN_PORT}:80\"/" docker-compose.yml
    sed -i '' "s/\"3306:3306\"/\"${MYSQL_PORT}:3306\"/" docker-compose.yml
    sed -i '' "s/image: laravel-app/image: ${PROJECT_NAME}-app/" docker-compose.yml
    sed -i '' "/^networks:/,/^volumes:/ s/laravel:/laravel:\n    name: ${PROJECT_NAME}_network/" docker-compose.yml
    sed -i '' "/^volumes:/,$ s/mysql_data:/mysql_data:\n    name: ${PROJECT_NAME}_mysql_data/" docker-compose.yml
else
    # Linux
    sed -i "s/container_name: laravel-app/container_name: ${PROJECT_NAME}-app/" docker-compose.yml
    sed -i "s/container_name: laravel-nginx/container_name: ${PROJECT_NAME}-nginx/" docker-compose.yml
    sed -i "s/container_name: laravel-mysql/container_name: ${PROJECT_NAME}-mysql/" docker-compose.yml
    sed -i "s/container_name: laravel-phpmyadmin/container_name: ${PROJECT_NAME}-phpmyadmin/" docker-compose.yml
    sed -i "s/\"8000:80\"/\"${APP_PORT}:80\"/" docker-compose.yml
    sed -i "s/\"8080:80\"/\"${PHPMYADMIN_PORT}:80\"/" docker-compose.yml
    sed -i "s/\"3306:3306\"/\"${MYSQL_PORT}:3306\"/" docker-compose.yml
    sed -i "s/image: laravel-app/image: ${PROJECT_NAME}-app/" docker-compose.yml
    sed -i "/^networks:/,/^volumes:/ s/laravel:/laravel:\n    name: ${PROJECT_NAME}_network/" docker-compose.yml
    sed -i "/^volumes:/,$ s/mysql_data:/mysql_data:\n    name: ${PROJECT_NAME}_mysql_data/" docker-compose.yml
fi

echo "✓ docker-compose.yml updated"

# Update docker-setup.sh
echo "Updating docker-setup.sh..."
if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' "s|APP_URL=http://localhost:8000|APP_URL=http://localhost:${APP_PORT}|" docker-setup.sh
    sed -i '' "s|http://localhost:8000|http://localhost:${APP_PORT}|g" docker-setup.sh
else
    sed -i "s|APP_URL=http://localhost:8000|APP_URL=http://localhost:${APP_PORT}|" docker-setup.sh
    sed -i "s|http://localhost:8000|http://localhost:${APP_PORT}|g" docker-setup.sh
fi

chmod +x docker-setup.sh

echo "✓ docker-setup.sh updated"

# Create a ports reference file
cat > DOCKER-PORTS.md << EOF
# Docker Ports for $PROJECT_NAME

## Access URLs
- **Application**: http://localhost:$APP_PORT
- **phpMyAdmin**: http://localhost:$PHPMYADMIN_PORT

## Database Connection
- **Host**: localhost (or \`mysql\` from inside containers)
- **Port**: $MYSQL_PORT
- **Database**: laravel (or change in .env)
- **Username**: laravel
- **Password**: secret

## Container Names
- App: ${PROJECT_NAME}-app
- Nginx: ${PROJECT_NAME}-nginx
- MySQL: ${PROJECT_NAME}-mysql
- phpMyAdmin: ${PROJECT_NAME}-phpmyadmin

## Quick Start
\`\`\`bash
./docker-setup.sh
\`\`\`

## Quick Commands
\`\`\`bash
make up          # Start containers
make down        # Stop containers
make logs        # View logs
make shell       # Access shell
make open        # Open app in browser
make phpmyadmin  # Open phpMyAdmin
\`\`\`
EOF

echo "✓ DOCKER-PORTS.md created"

echo ""
echo "=========================================="
echo "✅ Docker setup copied successfully!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. cd $TARGET_PATH"
echo "2. Update .env file with:"
echo "   - APP_URL=http://localhost:$APP_PORT"
echo "   - CACHE_DRIVER=file"
echo "   - SESSION_DRIVER=file"
echo "   - QUEUE_CONNECTION=sync"
echo "3. Run: ./docker-setup.sh"
echo ""
echo "Your project will be available at:"
echo "  - App: http://localhost:$APP_PORT"
echo "  - phpMyAdmin: http://localhost:$PHPMYADMIN_PORT"
echo ""

