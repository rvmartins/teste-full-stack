FROM php:7.4-apache

# Instala dependências de sistema
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões do PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Habilita mod_rewrite
RUN a2enmod rewrite

# Define o DocumentRoot para /var/www/html/public
# e ativa AllowOverride para .htaccess funcionar
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && echo "<Directory /var/www/html/public>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>" >> /etc/apache2/apache2.conf

# Copia o Composer (de imagem oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho padrão
WORKDIR /var/www/html

# Expõe a porta 80 (default Apache)
EXPOSE 80
