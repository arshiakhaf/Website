#!/bin/sh
# ============================================================
#  دستورات مفید برای اجرا/تست سایت
#  استفاده:  bash bin/cli.sh [فرمان]
# ============================================================
set -e
cd "$(dirname "$0")/.."

case "${1:-help}" in
    provision)
        docker compose exec -T web php /arian/bin/provision.php --force ;;
    logs)
        docker compose logs -f web ;;
    db-logs)
        docker compose logs -f db ;;
    shell)
        docker compose exec web bash ;;
    cli)
        docker compose exec -T web php /arian/bin/provision.php --help ;;
    restart)
        docker compose restart web ;;
    status)
        docker compose ps ;;
    backup)
        mkdir -p backups
        docker compose exec -T db sh -c 'exec mysqldump -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' > "backups/arian-$(date +%Y%m%d-%H%M%S).sql"
        echo "پشتیبان در backups/ ذخیره شد." ;;
    *)
        cat <<EOF
فرمان‌های در دسترس:
  bash bin/cli.sh provision   ← اجرای مجدد نصب/داده نمونه (با --force)
  bash bin/cli.sh logs        ← مشاهده لاگ وب‌سرور
  bash bin/cli.sh shell       ← ورود به شل کانتینر
  bash bin/cli.sh restart     ← ری‌استارت وب‌سرور
  bash bin/cli.sh status      ← وضعیت کانتینرها
  bash bin/cli.sh backup      ← پشتیبان‌گیری دیتابیس
EOF
        ;;
esac
