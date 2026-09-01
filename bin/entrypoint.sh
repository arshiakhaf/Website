#!/bin/sh
# ============================================================
#  نقطه شروع کانتینر وب — نصب خودکار وردپرس ووکامرس
# ============================================================
set -e

DB_HOST="${DB_HOST:-db}"
DB_NAME="${DB_NAME:-arian}"
DB_USER="${DB_USER:-arian}"
DB_PASSWORD="${DB_PASSWORD:-arian_pass_2026}"
SITE_URL="${SITE_URL:-http://localhost:8080}"
WP_ADMIN_USER="${WP_ADMIN_USER:-admin}"
WP_ADMIN_PASSWORD="${WP_ADMIN_PASSWORD:-Admin@1234}"
WP_ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@arian-shop.local}"

echo "[arian] waiting for database ${DB_HOST} ..."
i=0
until php -r '
    $h = getenv("DB_HOST"); $u = getenv("DB_USER"); $p = getenv("DB_PASSWORD"); $d = getenv("DB_NAME");
    $m = @mysqli_connect($h, $u, $p, $d);
    if ($m) { echo "ok"; exit(0); }
    exit(1);
' >/dev/null 2>&1; do
    i=$((i + 1))
    if [ "$i" -ge 60 ]; then
        echo "[arian] FATAL: database not reachable after 60 tries" >&2
        exit 1
    fi
    sleep 2
done
echo "[arian] database is ready."

# --- دایرکتوری آپلود ---
WP_ROOT=/var/www/html
mkdir -p "$WP_ROOT/wp-content/uploads/2026/09"
chown -R www-data:www-data "$WP_ROOT/wp-content/uploads" 2>/dev/null || true

# --- ساخت wp-config.php در اولین اجرا ---
if [ ! -f "$WP_ROOT/wp-config.php" ]; then
    echo "[arian] creating wp-config.php ..."
    AUTH_KEY=$(php -r 'echo bin2hex(random_bytes(48));')
    SECURE_AUTH_KEY=$(php -r 'echo bin2hex(random_bytes(48));')
    LOGGED_IN_KEY=$(php -r 'echo bin2hex(random_bytes(48));')
    NONCE_KEY=$(php -r 'echo bin2hex(random_bytes(48));')
    AUTH_SALT=$(php -r 'echo bin2hex(random_bytes(48));')
    SECURE_AUTH_SALT=$(php -r 'echo bin2hex(random_bytes(48));')
    LOGGED_IN_SALT=$(php -r 'echo bin2hex(random_bytes(48));')
    NONCE_SALT=$(php -r 'echo bin2hex(random_bytes(48));')

    cat > "$WP_ROOT/wp-config.php" <<EOF
<?php
/**
 * پیکربندی خودکار سایت — کلیدهای امنیتی در اولین اجرا ساخته می‌شوند.
 */
define( 'DB_NAME',     '${DB_NAME}' );
define( 'DB_USER',     '${DB_USER}' );
define( 'DB_PASSWORD', '${DB_PASSWORD}' );
define( 'DB_HOST',     '${DB_HOST}' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

define( 'AUTH_KEY',         '${AUTH_KEY}' );
define( 'SECURE_AUTH_KEY',  '${SECURE_AUTH_KEY}' );
define( 'LOGGED_IN_KEY',    '${LOGGED_IN_KEY}' );
define( 'NONCE_KEY',        '${NONCE_KEY}' );
define( 'AUTH_SALT',        '${AUTH_SALT}' );
define( 'SECURE_AUTH_SALT', '${SECURE_AUTH_SALT}' );
define( 'LOGGED_IN_SALT',   '${LOGGED_IN_SALT}' );
define( 'NONCE_SALT',       '${NONCE_SALT}' );

\$table_prefix = 'wp_';

define( 'WP_HOME',    '${SITE_URL}' );
define( 'WP_SITEURL', '${SITE_URL}' );
define( 'WP_DEBUG', false );
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'FS_METHOD', 'direct' );
define( 'DISABLE_WP_CRON', true );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'AUTOMATIC_UPDATER_DISABLED', true );

/* حالت کاملاً آفلاین: از ارسال درخواست به بیرون جلوگیری می‌کند */
if ( ! defined( 'ARIAN_OFFLINE' ) ) {
    define( 'ARIAN_OFFLINE', true );
}

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
EOF
    echo "[arian] wp-config.php created."
else
    echo "[arian] wp-config.php already exists."
fi

# --- نصب / داده نمونه (idempotent) ---
echo "[arian] provisioning (install + demo data) ..."
php /arian/bin/provision.php || {
    echo "[arian] FATAL: provisioning failed." >&2
    exit 1
}

chown -R www-data:www-data "$WP_ROOT/wp-content" 2>/dev/null || true

echo "[arian] ready. site=${SITE_URL}  admin=${SITE_URL}/wp-admin"
exec "$@"
