# ============================================================
#  تصویر وب‌سرور فروشگاه آرین‌شاپ
#  PHP 8.2 + Apache + افزونه‌های لازم وردپرس/ووکامرس
# ============================================================
FROM php:8.2-apache

# --- ابزارهای سیستم و افزونه‌های PHP ---
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        mariadb-client \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libxslt1-dev \
        libxml2-dev \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        mysqli pdo_mysql gd zip intl bcmath mbstring exif \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# --- پیکربندی Apache و PHP ---
COPY config/apache-default.conf /etc/apache2/conf-available/arian.conf
COPY config/php.ini /usr/local/etc/php/conf.d/arian.ini
RUN a2enconf arian \
    && sed -ri 's/^ServerTokens .*/ServerTokens Prod/' /etc/apache2/conf-available/security.conf

# --- کد سایت (وردپرس + قالب + افزونه‌ها) ---
COPY wordpress/ /var/www/html/

# --- اسکریپت‌های نصب/راه‌اندازی ---
COPY bin/ /arian/bin/
RUN chmod +x /arian/bin/*.sh /arian/bin/*.php

# دایرکتوری آپلود همیشه قابل نوشتن باشد
RUN mkdir -p /var/www/html/wp-content/uploads \
    && chown -R www-data:www-data /var/www/html/wp-content/uploads \
    && rm -f /var/www/html/wp-content/uploads/index.php

# هنگام بالا آمدن کانتینر، وب‌سرور را با نصب خودکار اجرا می‌کنیم
ENTRYPOINT ["/arian/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
