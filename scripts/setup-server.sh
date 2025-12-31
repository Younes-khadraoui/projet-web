#!/bin/bash
set -e

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║         e-bazar Application Server Setup                       ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_DIR"

echo "Project directory: $PROJECT_DIR"
echo ""

if [ -f .env ]; then
    echo "✓ .env already exists"
else
    echo "Creating .env from template..."
    if [ -f .env.production ]; then
        cp .env.production .env
    else
        cp .env.example .env
    fi
    echo "✓ .env created - EDIT IT with your settings!"
fi

echo ""
echo "Setting up directories and permissions..."

mkdir -p public/uploads
chmod 755 public/uploads

# Set permissions
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.css" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

echo "✓ Permissions set"

echo ""
echo "Testing database connection..."

if [ -f .env ]; then
    set -a
    source .env
    set +a
    
    echo "   Host: $DB_HOST"
    echo "   Database: $DB_NAME"
    echo "   User: $DB_USER"
    
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "SELECT VERSION();" > /dev/null 2>&1; then
        echo "✓ Database connection successful"
    else
        echo "✗ Database connection failed"
        echo "  Check your DB credentials in .env"
    fi
else
    echo "Cannot test - .env file missing"
fi

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                   Setup Complete!                              ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""
echo "Next steps:"
echo ""
echo "1. Edit .env with your environment settings:"
echo "   nano .env"
echo ""
echo "2. Verify Apache configuration:"
echo "   - DocumentRoot: $PROJECT_DIR/public"
echo "   - Ensure mod_rewrite is enabled"
echo ""
echo "3. Test the application:"
echo "   curl http://\$(hostname -I | awk '{print $1}')/"
echo ""
echo "4. Check logs if there are issues:"
echo "   tail -f /var/log/apache2/error.log"
echo ""
