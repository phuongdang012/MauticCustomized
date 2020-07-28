FROM php:7.3-apache

LABEL vendor="Mautic"
LABEL maintainer="Phuong Dang Tran Thanh"

# Install PHP extensions
RUN apt-get update && apt-get install --no-install-recommends -y \
    ca-certificates \
    build-essential  \
    software-properties-common \
    cron \
    git \
    htop \
    wget \
    dos2unix \
    sudo \
    libkrb5-dev \
    libc-client-dev \
    libicu-dev \
    libkrb5-dev \
    libmcrypt-dev \
    libssl-dev \
    libz-dev \
    libzip-dev \
    libmagickwand-dev \
    imagemagick \
    libpng-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libxpm-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    nano \
    zip \
    mariadb-client \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/* \
    && rm /etc/cron.daily/*

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install imap && \
    docker-php-ext-enable imap

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/lib --with-png-dir=/usr/lib --with-jpeg-dir=/usr/lib \
    && docker-php-ext-install gd \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install intl mbstring mysqli pdo_mysql zip opcache bcmath gd \
    && docker-php-ext-enable intl mbstring mysqli pdo_mysql zip opcache bcmath gd

RUN docker-php-ext-install sockets

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Define Mautic volume to persist data
VOLUME /var/www/html

# By default enable cron jobs
ENV MAUTIC_RUN_CRON_JOBS true

# Setting an root user for test
ENV MAUTIC_DB_USER root
ENV MAUTIC_DB_NAME mautic

# Setting PHP properties
ENV PHP_INI_DATE_TIMEZONE='Asia/Ho_Chi_Minh' \
    PHP_MEMORY_LIMIT=512M \
    PHP_MAX_UPLOAD=128M \
    PHP_MAX_EXECUTION_TIME=300

# Download git and SSH support for cloning repository
RUN apt-get update
RUN apt-get install -y git
RUN apt-get -yq update && \
    apt-get -yqq install ssh

# Add SSH private key to git
RUN mkdir /root/.ssh/
ADD id_rsa /root/.ssh/id_rsa
RUN chmod 600 ~/.ssh/id_rsa
RUN touch /root/.ssh/known_hosts
RUN ssh-keyscan bitbucket.org >> /root/.ssh/known_hosts

#Create folder to clone repo and restore dependencies
RUN mkdir /usr/src/mautic
RUN sudo git clone -b dev/origin git@bitbucket.org:phuongdang012/mautic3-custom.git /usr/src/mautic
RUN cd /usr/src/mautic \
    && sudo composer install

#Setup owner and permissions
RUN chown -R www-data:www-data /usr/src/mautic/
RUN chmod -R 777 /usr/src/mautic/
RUN chown -R www-data:www-data /var/www/html/


# Copy init scripts and custom .htaccess
# COPY . /usr/src/mautic
RUN ls /usr/src/mautic
COPY docker-entrypoint.sh /entrypoint.sh
COPY makeconfig.php /makeconfig.php
COPY makedb.php /makedb.php
COPY mautic.crontab /etc/cron.d/mautic
RUN chmod 655 /etc/cron.d/mautic

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Apply necessary permissions
RUN ["chmod", "+x", "/entrypoint.sh"]
ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]
RUN chmod -R 777 /var/www/html/