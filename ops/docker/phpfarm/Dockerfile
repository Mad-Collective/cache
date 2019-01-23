FROM splitbrain/phpfarm:jessie

RUN apt-get update
RUN apt-get install -y --no-install-recommends unzip wget autoconf build-essential

ENV REDIS_HOST redis

RUN wget https://github.com/phpredis/phpredis/archive/master.zip -O /tmp/phpredis-master.zip

RUN mkdir /tmp/phpredis-7.1
RUN unzip -o /tmp/phpredis-master.zip -d /tmp/phpredis-7.1
WORKDIR /tmp/phpredis-7.1/phpredis-master
RUN phpize-7.1
RUN ./configure --with-php-config=php-config-7.1
RUN make
RUN make install
RUN echo 'extension=redis.so' >> /phpfarm/inst/php-7.1.25/etc/php.ini

RUN mkdir /tmp/phpredis-7.2
RUN unzip -o /tmp/phpredis-master.zip -d /tmp/phpredis-7.2
WORKDIR /tmp/phpredis-7.2/phpredis-master
RUN phpize-7.2
RUN ./configure --with-php-config=php-config-7.2
RUN make
RUN make install
RUN echo 'extension=redis.so' >> /phpfarm/inst/php-7.2.13/etc/php.ini

WORKDIR /app/cache
VOLUME ["/app/cache"]

RUN curl -sS https://getcomposer.org/installer | php-5.6 && mv /app/cache/composer.phar /usr/local/bin/composer