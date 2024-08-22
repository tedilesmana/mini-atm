##############################################################
##############################################################
##############################################################
FROM php:8.1.0-fpm

RUN apt-get update && apt upgrade -y && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && apt-get install -y \
        netcat-openbsd \
        curl \
        sed \
        zlib1g-dev \
        git \
        zip \
        unzip \
        nano \
        openssl \
        libc6-dev \
        libsasl2-dev \
        libsasl2-modules \
        libssl-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*

RUN apt-get install git -y

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

RUN apt-get update -y
RUN apt install libsodium-dev -y
RUN apt-get install webp -y
RUN apt-get install npm -y
RUN apt-get install -y mariadb-client
RUN docker-php-ext-install gd
RUN docker-php-ext-install intl
RUN docker-php-ext-install sodium
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN ( \
        cd /tmp \
        && mkdir librdkafka \
        && cd librdkafka \
        && git clone https://github.com/edenhill/librdkafka.git . \
        && ./configure \
        && make \
        && make install \
    )
RUN apt-get install -y librdkafka-dev && pecl install rdkafka
RUN echo "extension=rdkafka.so" >> /usr/local/etc/php/conf.d/rdkafka.ini
RUN apt-get install -y cron
RUN apt-get update && apt-get install -y libpq-dev

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-configure intl && docker-php-ext-install intl

RUN apt-get update \
     && apt-get install -y libzip-dev \
     && docker-php-ext-install zip

RUN apt-get update && apt-get install -y \
    netcat-traditional

RUN apt-get update && apt-get install -y busybox
RUN nc -h

COPY . /var/www/html
WORKDIR /var/www/html

# Ensure the storage and cache directories are writable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000

# Command to run the entrypoint script
ENTRYPOINT ["./entrypoint.sh"]
