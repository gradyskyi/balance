FROM php:7.4.4-fpm

## Install recommended extensions for Symfony
RUN apt-get update --fix-missing \
    && apt-get install -y --no-install-recommends \
#       for composer
        git \
#       for zip php extension
        libzip-dev \
#       for php intl extension
        libicu-dev \
#       for php pdo_pgsql extension
        libpq-dev \
#       for php http extension
        libcurl4-openssl-dev \
#       for php http extension
        libssl-dev \
        unzip \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        opcache \
        intl \
        zip

ENV ACCEPT_EULA=Y

# Install php raphf extension (need for php http extension)
ADD https://pecl.php.net/get/raphf/2.0.0 /tmp/raphf.tar.gz
RUN mkdir -p /usr/src/php/ext/raphf\
    && tar xf /tmp/raphf.tar.gz -C /usr/src/php/ext/raphf --strip-components=1\
    && docker-php-ext-configure raphf\
    && docker-php-ext-install raphf\
    && rm /tmp/raphf.tar.gz

# Install php propro extension (need for php http extension)
ADD https://pecl.php.net/get/propro/2.1.0 /tmp/propro.tar.gz
RUN mkdir -p /usr/src/php/ext/propro\
    && tar xf /tmp/propro.tar.gz -C /usr/src/php/ext/propro --strip-components=1\
    && docker-php-ext-configure propro\
    && docker-php-ext-install propro\
    && rm /tmp/propro.tar.gz

# Install php http extension
ADD https://pecl.php.net/get/pecl_http/3.2.0 /tmp/http.tar.gz
RUN mkdir -p /usr/src/php/ext/http\
    && tar xf /tmp/http.tar.gz -C /usr/src/php/ext/http --strip-components=1\
    && docker-php-ext-configure http\
    && docker-php-ext-install http\
    && rm /tmp/http.tar.gz

ENV COMPOSER_ALLOW_SUPERUSER 1
# Install composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php\
    && chmod +x composer.phar\
    && mv composer.phar /usr/local/bin/composer

# Change www-data user to match the host system UID and GID and chown www directory
RUN usermod --non-unique --uid 1000 www-data \
  && groupmod --non-unique --gid 1000 www-data \
  && chown -R www-data:www-data /var/www
  
USER www-data