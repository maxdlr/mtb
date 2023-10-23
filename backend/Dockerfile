FROM php:latest

WORKDIR /app

COPY . /app

RUN apt-get update --fix-missing

RUN apt-get install aptitude -y

RUN aptitude install -y \
    composer \
    make \
    curl

# Enable php8-xdebug if $PHP_XDEBUG_MODE is not empty
ARG PHP_XDEBUG_MODE=off
ARG PHP_XDEBUG_CLIENT_PORT=5902
ARG PHP_XDEBUG_CLIENT_HOST=host.docker.internal
COPY xdebug.ini  /etc/php8/conf.d/xdebug.ini.template
RUN if [[ "$PHP_XDEBUG_MODE" != "" ]]; then \
        apk add --no-cache php8-pecl-xdebug; \
        export PHP_XDEBUG_MODE=$PHP_XDEBUG_MODE; \
        export PHP_XDEBUG_CLIENT_PORT=$PHP_XDEBUG_CLIENT_PORT; \
        export PHP_XDEBUG_CLIENT_HOST=$PHP_XDEBUG_CLIENT_HOST; \
        envsubst < /etc/php8/conf.d/xdebug.ini.template > /etc/php8/conf.d/xdebug.ini; \
    fi
RUN rm -f /etc/php8/conf.d/xdebug.ini.template

# RUN curl -sS https://getcomposer.org/installer | tee composer-setup.php \
#     && php8 composer-setup.php && rm composer-setup.php* \
#     && chmod +x composer.phar && mv composer.phar /usr/bin/composer \
#     && ln -s /usr/bin/php8 /usr/local/bin/php

RUN aptitude install -y \
    nodejs \
    npm
    
RUN npm install --global yarn

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh'
RUN apt-get install -y symfony-cli

COPY symfony.ini /etc/php8/conf.d/
COPY symfony.ini /etc/php8/cli/conf.d/

COPY symfony.pool.conf /etc/php8/php-fpm.d/

RUN composer install

RUN symfony console c:c
RUN symfony console d:d:c
RUN symfony console d:m:m
RUN symfony console d:f:l

CMD ["symfony", "server:start"]

EXPOSE 9001