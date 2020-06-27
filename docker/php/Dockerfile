FROM alpine:3.10

LABEL maintainer="Aurélien Tournayre <aurelien.tournayre@gmail.com>"

RUN apk add --update \
    php7-fpm \
    php7-apcu \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-gd \
    php7-iconv \
    php7-imagick \
    php7-json \
    php7-intl \
    php7-mcrypt \
    php7-fileinfo \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_mysql \
    php7-mysqli \
    php7-xml \
    php7-zlib \
    php7-phar \
    php7-tokenizer \
    php7-session \
    php7-simplexml \
    php7-xmlwriter \
#    php7-xdebug \
    php7-zip \
    make \
    curl \
    nodejs \
    yarn

#RUN rm -rf /var/cache/apk/* && rm -rf /tmp/* && \
#    curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
RUN	apk update && \
	apk upgrade && \
	apk add --update curl openssl && \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.6 && \
	chmod +x /usr/local/bin/composer && \
	apk del curl openssl && \
	rm -rf /var/cache/apk/*

ADD php.ini /etc/php7/conf.d/
ADD php.ini /etc/php7/cli/conf.d/
ADD php-fpm.conf /etc/php7/php-fpm.d/
#ADD xdebug.ini  /etc/php7/conf.d/

WORKDIR /var/www/symfony

RUN adduser -D -g 'www' www \
    && chown -R www:www /var/www

EXPOSE 9001

CMD ["php-fpm7", "-F"]