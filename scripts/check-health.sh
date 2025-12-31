#!/bin/bash

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║            e-bazar Application Health Check                    ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_DIR"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "Project: $PROJECT_DIR"
echo ""

echo "1. Environment Configuration"
if [ -f .env ]; then
    echo -e "${GREEN}   ✓ .env exists${NC}"
    source .env
else
    echo -e "${RED}   ✗ .env missing${NC}"
fi

echo ""

# Check directories
echo "2. Directory Structure"
DIRS=("app" "public" "public/assets" "public/uploads")
for dir in "${DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo -e "${GREEN}   ✓ $dir/${NC}"
    else
        echo -e "${RED}   ✗ $dir/ missing${NC}"
    fi
done

echo ""

# Check key files
echo "3. Key Files"
FILES=("public/index.php" "public/assets/css/style.css" "app/core/database.php")
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}   ✓ $file${NC}"
    else
        echo -e "${RED}   ✗ $file missing${NC}"
    fi
done

echo ""

# Check database
echo "4. Database Connection"
if [ -z "$DB_HOST" ]; then
    source .env
fi

if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "USE $DB_NAME; SELECT 1;" > /dev/null 2>&1; then
    echo -e "${GREEN}   ✓ Database connected (${DB_HOST}/${DB_NAME})${NC}"
else
    echo -e "${RED}   ✗ Database connection failed${NC}"
fi

echo ""

# Check web access
echo "5. Web Server"
if [ ! -z "$BASE_URL" ]; then
    RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/")
    if [ "$RESPONSE" = "200" ]; then
        echo -e "${GREEN}   ✓ Web server responding (HTTP $RESPONSE)${NC}"
        echo "      URL: $BASE_URL"
    else
        echo -e "${YELLOW}   ⚠ Web server responding with HTTP $RESPONSE${NC}"
        echo "      URL: $BASE_URL"
    fi 
else
    echo -e "${YELLOW}   ⚠ BASE_URL not configured${NC}"
fi

echo ""

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                   Health Check Complete                        ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""