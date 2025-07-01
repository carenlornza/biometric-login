# Gunakan image PHP + Apache
FROM php:8.2-apache

# Aktifkan mod_rewrite (penting untuk Laravel atau .htaccess routing)
RUN a2enmod rewrite

# Install ekstensi database (gunakan sesuai kebutuhan)
RUN docker-php-ext-install pdo pdo_mysql    # Untuk PDO (kamu pakai ini)
RUN docker-php-ext-install mysqli           # Tambahan jika kamu juga pakai mysqli

# Salin semua file ke folder web server
COPY . /var/www/html/

# Pastikan permission benar agar bisa diakses Apache
RUN chown -R www-data:www-data /var/www/html/

# Buka port 80 di container
EXPOSE 80
