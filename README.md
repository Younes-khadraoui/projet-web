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

## Installation

To set up the project for the first time, follow these steps:

1.  **Clone the repository:**

    ```bash
    git clone <repository-url>
    cd projet-web
    ```

2.  **Open the Installer:**
    Navigate to `http://your-local-server.com/install` in your web browser.

3.  **Configure Database:**
    You will see an installation page. Fill in your database credentials:

    - **Host:** `localhost` (or your database server address)
    - **Database Name:** The name you want for your database (e.g., `ebazar_db`)
    - **User:** Your database username
    - **Password:** Your database password

4.  **Install:**
    Click the "Lancer l'installation" button. The installer will create the database, set up the tables, and generate a `.env` file with your configuration.

5.  **Security Warning:**
    After a successful installation, you will be prompted to **delete the `/install` directory**. This is a critical security measure to prevent anyone from re-running the installer.

    ```bash
    rm -rf install
    ```

6.  **Done!**
    You can now access the application from the `public` directory.

## Configuration

### Environment Variables

Copy the example environment file and configure your settings:

```bash
cp .env.example .env
nano .env
```

### Key Generation

Generate a new application key. This step is crucial for securing user sessions and other encrypted data:

```bash
php artisan key:generate
```

### Database
