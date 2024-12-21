# Sử dụng image PHP chính thức với Apache
FROM php:8.1-apache

# Copy mã nguồn vào thư mục /var/www/html của container
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
