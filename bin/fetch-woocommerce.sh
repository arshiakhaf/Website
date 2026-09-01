#!/bin/sh
# ============================================================
#  دریافت/به‌روزرسانی WooCommerce از wordpress.org (نیاز به اینترنت)
#  این اسکریپت فقط وقتی می‌خواهید آخرین نسخه ووکامرس را نصب کنید اجرا می‌شود.
#  نسخه پشتیبان قبلی در wp-content/plugins/woocommerce.bak نگه داشته می‌شود.
# ============================================================
set -e
cd "$(dirname "$0")/.."

PLUGIN_DIR="wordpress/wp-content/plugins"
URL="https://downloads.wordpress.org/plugin/woocommerce.zip"
TMP=".tmp-woocommerce"

echo "در حال دریافت آخرین نسخه ووکامرس از wordpress.org ..."
if ! curl -fsSL --max-time 300 -o "$TMP.zip" "$URL"; then
    echo "خطا: دانلود ناموفق بود (اینترنت در دسترس نیست؟)." >&2
    exit 1
fi

rm -rf "$TMP"
mkdir -p "$TMP"
if ! unzip -q "$TMP.zip" -d "$TMP"; then
    echo "خطا: فایل فشرده خراب است." >&2
    exit 1
fi

NEW="$TMP/woocommerce"
[ -d "$NEW" ] || { echo "خطا: محتوای پلاگین پیدا نشد." >&2; exit 1; }

# پشتیبان‌گیری و جایگزینی
if [ -d "$PLUGIN_DIR/woocommerce" ]; then
    rm -rf "$PLUGIN_DIR/woocommerce.bak"
    mv "$PLUGIN_DIR/woocommerce" "$PLUGIN_DIR/woocommerce.bak"
fi
mv "$NEW" "$PLUGIN_DIR/woocommerce"
rm -rf "$TMP" "$TMP.zip"

echo "✅ ووکامرس به‌روزرسانی شد. حالا: docker compose up -d --build"
echo "   (نسخه قبلی در woocommerce.bak نگه داشته شده است)"
