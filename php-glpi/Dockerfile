FROM lamasbr/docker-php:7.2-fpm

RUN \
    pecl install apcu \
    && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini \
    && docker-php-ext-install xmlrpc \
	&& sed -ri 's/^www-data:x:33:33:/www-data:x:1000:50:/' /etc/passwd
