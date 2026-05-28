# =====================================================================
# Webpage — PHP 8.2 + Apache
# 빌드: docker compose build
# =====================================================================
FROM php:8.2-apache

# 1) PHP 확장 (PDO MySQL, mbstring, gd 등 + 한글/이미지 처리)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libfreetype6-dev \
        unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 2) Apache 모듈 — rewrite(.htaccess) + headers(보안헤더)
RUN a2enmod rewrite headers

# 3) PHP 설정 오버라이드 (업로드 크기·timezone)
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-custom.ini

# 4) Apache vhost — DocumentRoot 를 /var/www/html/public 로
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# 5) /var/www/ 디렉터리 AllowOverride 허용 (apache2.conf 패치)
RUN sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# 코드는 compose에서 볼륨 마운트 (개발) 또는 build 시 COPY (운영용 별도 Dockerfile)

EXPOSE 80
