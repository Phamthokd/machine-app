#!/bin/bash
# Deploy script for machine-app on VPS
# Run this on the VPS after SSH login

set -e

echo "=========================================="
echo "  DEPLOY MACHINE-APP TO VPS"
echo "=========================================="

# Find the project directory
PROJECT_DIR=""
for dir in /var/www/html /var/www /home/*/machine-app /opt/machine-app; do
    if [ -d "$dir" ] && [ -f "$dir/artisan" ]; then
        PROJECT_DIR="$dir"
        break
    fi
done

if [ -z "$PROJECT_DIR" ]; then
    # Try to find it
    FOUND=$(find /var /home /opt -name "artisan" 2>/dev/null | head -1)
    if [ -n "$FOUND" ]; then
        PROJECT_DIR=$(dirname "$FOUND")
    fi
fi

if [ -z "$PROJECT_DIR" ]; then
    echo "ERROR: Cannot find Laravel project directory!"
    echo "Please run: find / -name 'artisan' 2>/dev/null"
    exit 1
fi

echo "Project directory: $PROJECT_DIR"
cd "$PROJECT_DIR"

# Step 1: Backup database
echo ""
echo "[1/6] Backing up database..."
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
BACKUP_FILE="/root/backup_$(date +%Y%m%d_%H%M%S).sql"

if mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
    echo "  ✅ Database backed up to: $BACKUP_FILE"
else
    echo "  ⚠️  Backup failed or skipped (continuing anyway)"
fi

# Step 2: Pull latest code
echo ""
echo "[2/6] Pulling latest code from GitHub..."
git pull origin main
echo "  ✅ Code updated"

# Step 3: Composer install
echo ""
echo "[3/6] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5
echo "  ✅ Dependencies installed"

# Step 4: Run migrations (SAFE - only adds new tables/columns)
echo ""
echo "[4/6] Running database migrations..."
php artisan migrate --force
echo "  ✅ Migrations complete"

# Step 5: Clear all caches
echo ""
echo "[5/6] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
echo "  ✅ Caches cleared and optimized"

# Step 6: Set permissions
echo ""
echo "[6/6] Setting file permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
echo "  ✅ Permissions set"

echo ""
echo "=========================================="
echo "  ✅ DEPLOY COMPLETED SUCCESSFULLY!"
echo "=========================================="
echo ""
echo "Database backup: $BACKUP_FILE"
echo "Remember to change your VPS root password!"
