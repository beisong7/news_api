FROM php:8.2-apache

RUN apt-get update

# install platform plugins
RUN apt-get install -y \
    vim \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++

# configure apache and set document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

# start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# install php extension and plugins required for your specific project
RUN docker-php-ext-install \
    gd \
	bz2 \
	exif \
    intl \
	iconv \
	mysqli \
    bcmath \
	gettext \
    opcache \
	# mbstring \
    calendar \
    pdo_mysql

# more required plugins
RUN apt-get install libzip-dev -y

# 5. install composer for docker
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# you may decide to add a user add to www-data
RUN useradd -G www-data,root -u 1000 -d /home/devuser devuser
RUN mkdir -p /home/devuser/.composer && \
    chown -R devuser:devuser /home/devuser

# copy your project
COPY . /var/www/html

# RUN chown -R devuser:devuser /var/www/html
RUN chown -R www-data:www-data /var/www/html

# change mod for storage
RUN chmod -R ugo+rw /var/www/html/storage

# change mod for Laravel Error Log Permission
RUN chmod 777 /var/www/html/storage
RUN chmod 777 /var/www/html/run.sh

# set work directory again - might be redundant
WORKDIR /var/www/html

# install the app dependencies
RUN composer install

# setup env
COPY .env.example /var/www/html/.env

# activate application key
#RUN php artisan key:gen

# ENTRYPOINT ["/var/www/html/run.sh"]
# CMD [ "/var/www/html/run.sh" ]
# CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
