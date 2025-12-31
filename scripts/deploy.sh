#!/bin/bash
echo "=== e-bazar Deployment Script ==="
echo ""

if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
    echo "✓ .env created. Please edit it with your production settings."
fi

echo "Setting permissions..."
chmod 755 public/
chmod 755 public/uploads/
chmod 644 public/*.php
chmod 644 public/assets/css/*.css
chmod 644 app/core/*.php
chmod 644 app/controllers/*.php
chmod 644 app/models/*.php

echo "✓ Permissions set"
echo ""
echo "=== Deployment Complete ==="
echo ""
echo "Configuration checklist:"
echo "1. Edit .env with your server settings:"
echo "   - DB_HOST, DB_NAME, DB_USER, DB_PASSWORD"
echo "   - BASE_URL (your server domain/IP)"
echo "   - APP_ENV=production"
echo ""
echo "2. Ensure Apache DocumentRoot points to: $(pwd)/public/"
echo ""
echo "3. Test the application at: http://<your-server-url>"
