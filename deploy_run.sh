#!/bin/bash
cd /var/www/machine-app

echo "=== STEP 1: Backup Database ==="
DB_NAME=$(grep '^DB_DATABASE' .env | cut -d= -f2)
DB_USER=$(grep '^DB_USERNAME' .env | cut -d= -f2)
DB_PASS=$(grep '^DB_PASSWORD' .env | cut -d= -f2)
BACKUP="/root/backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP" 2>&1
if [ $? -eq 0 ]; then
    echo "DB backed up to: $BACKUP"
else
    echo "Backup warning - check credentials"
fi

echo ""
echo "=== STEP 2: Run Migrations ==="
php artisan migrate --force 2>&1
echo "Migration done"

echo ""
echo "=== STEP 3: Composer Install ==="
composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5
echo "Composer done"

echo ""
echo "=== STEP 4: Clear All Caches ==="
php artisan config:clear 2>&1
php artisan cache:clear 2>&1
php artisan view:clear 2>&1
php artisan route:clear 2>&1
php artisan optimize 2>&1
echo "Cache cleared"

echo ""
echo "=== STEP 5: Fix Permissions ==="
chmod -R 775 storage bootstrap/cache
chown -R nginx:nginx storage bootstrap/cache 2>/dev/null || chown -R apache:apache storage bootstrap/cache 2>/dev/null || chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
echo "Permissions set"

echo ""
echo "=============================="
echo "  DEPLOY COMPLETED!"
echo "=============================="
git log --oneline -3
