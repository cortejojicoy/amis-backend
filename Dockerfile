FROM php:8.1.4-cli

RUN apt-get update && apt-get install -y libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql pgsql \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
# RUN docker-php-ext-install pdo pdo_pgsql && docker-php-ext-enable pdo_pgsql

# Install composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN pecl install redis \
    && docker-php-ext-enable redis

# Set working directory
WORKDIR /uplbAPI
COPY . /uplbAPI

# Install dependencies
RUN composer install

# run
EXPOSE 80
CMD php artisan serve --host=0.0.0.0 --port=8000