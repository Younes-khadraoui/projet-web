## Projet web M1 GIL

# Avoid PHP 8 features: Don't use Constructor Property Promotion, Union Types, or the match expression unless you check if they exist in PHP 7.4.

# Deployment Workflow (Local -> VM)On On Arch the most efficient way to sync your work is using rsync from your terminal.To push your local code to the VM: rsync -avz --exclude='.git' ./ urouen@192.168.76.76:/var/www/html/
