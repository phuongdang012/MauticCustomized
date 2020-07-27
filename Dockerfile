FROM php:7.3-apache

LABEL vendor="Mautic"

LABEL maintainer="Trần Thành Phương Đăng <phuongdang012@gmail.com>"

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

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Define Mautic volume to persist data
VOLUME /var/www/html

# Define Mautic version and expected SHA1 signature
ENV MAUTIC_VERSION 3.0.1
ENV MAUTIC_SHA1 f0778e47ded0e3611607fce2b69989b8b9449551

# By default enable cron jobs
ENV MAUTIC_RUN_CRON_JOBS true

# Setting an Default database user for Mysql
ENV MAUTIC_DB_USER root

# Setting an Default database name for Mysql
ENV MAUTIC_DB_NAME mautic

# Setting PHP properties
ENV PHP_INI_DATE_TIMEZONE='Asia/Ho_Chi_Minh' \
    PHP_MEMORY_LIMIT=512M \
    PHP_MAX_UPLOAD=128M \
    PHP_MAX_EXECUTION_TIME=300

# Download package and extract to web volume
# RUN curl -o mautic.zip -SL https://github.com/mautic/mautic/releases/download/${MAUTIC_VERSION}/${MAUTIC_VERSION}.zip \

# Update aptitude with new repo
RUN apt-get update
# Install software
RUN apt-get install -y git
RUN apt-get -yq update && \
    apt-get -yqq install ssh

# Add SSH private key to git
RUN mkdir /root/.ssh/
ADD ./docker/id_rsa /root/.ssh/id_rsa
RUN chmod 600 ~/.ssh/id_rsa
RUN touch /root/.ssh/known_hosts
RUN ssh-keyscan bitbucket.org >> /root/.ssh/known_hosts

RUN git clone git@bitbucket.org:phuongdang012/mautic3-custom.git \
    # && echo "$MAUTIC_SHA1 *mautic.zip" | sha1sum -c - \
    && mkdir /usr/src/mautic \
    # && unzip mautic.zip -d /usr/src/mautic \
    # && rm mautic.zip \
    && chown -R www-data:www-data /usr/src/mautic

# Copy init scripts and custom .htaccess
COPY ./docker/docker-entrypoint.sh /entrypoint.sh
COPY ./docker/makeconfig.php /makeconfig.php
COPY ./docker/makedb.php /makedb.php
COPY ./docker/mautic.crontab /etc/cron.d/mautic
RUN chmod 644 /etc/cron.d/mautic

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Apply necessary permissions
RUN ["chmod", "+x", "/entrypoint.sh"]
ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]