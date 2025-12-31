## Projet web M1 GIL

### Documentation

#### Local Development
```bash
cd public
php -S localhost:8000
# Visit: http://localhost:8000
```

#### Server Deployment
```bash
cd /var/www/html/projet-web
git pull
cp .env.production .env
nano .env
bash scripts/deploy-production.sh
bash scripts/check-health.sh
```

### Database Setup

The project uses MySQL with the following default credentials:
- Database: `projet`
- User: `projet`
- Password: `tejorp`

Tables are created by the installer at `/install/`

### Avoid PHP 8 features
Don't use Constructor Property Promotion, Union Types, or the match expression unless you check if they exist in PHP 7.4.

### Deployment Workflow (Local -> VM)
On Arch the most efficient way to sync your work is using rsync from your terminal.
To push your local code to the VM:
```bash
rsync -avz --exclude='.git' ./ urouen@192.168.76.76:/var/www/html/
```

Or use the provided deployment scripts:
```bash
bash scripts/deploy-production.sh
```

