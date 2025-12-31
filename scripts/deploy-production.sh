#!/bin/bash
# scripts/deploy-production.sh - Complete production deployment script

set -e

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║       e-bazar Production Deployment Script                     ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_DIR="/var/www/html/projet-web"

if [ ! -d "$PROJECT_DIR" ]; then
    echo -e "${RED}Error: Project directory not found at $PROJECT_DIR${NC}"
    exit 1
fi

cd "$PROJECT_DIR"

echo "Working directory: $PROJECT_DIR"
echo ""

# Step 1: Update code
echo "Pulling latest code from git..."
if git pull origin main 2>/dev/null || git pull origin master 2>/dev/null; then
    echo -e "${GREEN}✓ Code updated${NC}"
else
    echo -e "${YELLOW}⚠ Git pull had issues - continuing anyway${NC}"
fi

echo ""

# Step 2: Check .env
echo "Checking environment configuration..."
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env from template...${NC}"
    cp .env.production .env
    echo -e "${RED}✗ IMPORTANT: Edit .env with your database credentials${NC}"
    echo "  nano .env"
    exit 1
else
    echo -e "${GREEN}✓ .env exists${NC}"
fi

echo ""

# Step 3: Check database connection
echo "🗄️  Verifying database connection..."
set -a
source .env
set +a

if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "USE $DB_NAME; SELECT 1;" > /dev/null 2>&1; then
    echo -e "${GREEN}✓ Database connection OK${NC}"
else
    echo -e "${RED}✗ Cannot connect to database${NC}"
    echo "   Check DB_HOST, DB_NAME, DB_USER, DB_PASSWORD in .env"
    exit 1
fi

echo ""

# Step 4: Set permissions
echo "Setting file permissions..."
chmod 755 public/
mkdir -p public/uploads
chmod 777 public/uploads
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.css" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
echo -e "${GREEN}✓ Permissions set${NC}"

echo ""

# Step 5: Check Apache configuration
echo "Checking Apache configuration..."
if [ -f scripts/apache-vhost.conf ]; then
    echo "   Apache vhost config available at: scripts/apache-vhost.conf"
    echo "   To enable:"
    echo "   $ sudo cp scripts/apache-vhost.conf /etc/apache2/sites-available/projet-web.conf"
    echo "   $ sudo a2ensite projet-web"
    echo "   $ sudo systemctl reload apache2"
fi

echo ""

# Step 6: Verify directories
echo "Verifying required directories..."
[ -d "app" ] && echo -e "${GREEN}✓ app/$(NC)" || echo -e "${RED}✗ app/${NC}"
[ -d "public" ] && echo -e "${GREEN}✓ public/${NC}" || echo -e "${RED}✗ public/${NC}"
[ -d "public/uploads" ] && echo -e "${GREEN}✓ public/uploads/${NC}" || echo -e "${RED}✗ public/uploads/${NC}"
[ -d "public/assets" ] && echo -e "${GREEN}✓ public/assets/${NC}" || echo -e "${RED}✗ public/assets/${NC}"

echo ""

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║              Deployment Complete!                              ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""
echo "✓ Code pulled and configured"
echo "✓ Database connection verified"
echo "✓ Permissions set"
echo ""
echo "Next steps:"
echo "1. Verify BASE_URL in .env matches your server URL"
echo "2. Test the application: curl http://192.168.76.76/"
echo "3. Check error logs: tail -f /var/log/apache2/error.log"
echo ""
echo "Application URL: http://$BASE_URL"
echo ""
