FROM thedrum/php:7.2-fpm

ENV COMPOSER_HOME /composer

RUN apt-get install -y unzip \
    && rm -rf /var/lib/apt/lists/*

# Download and verify Composer installer.
RUN curl --output /tmp/installer.sig --silent --fail --location --retry 5 \
    https://composer.github.io/installer.sig \
    && curl --output /tmp/installer.php --silent --fail --location --retry 5 \
    https://getcomposer.org/installer \
    && php -r " \
        \$signature = rtrim(file_get_contents('/tmp/installer.sig')); \
        \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
        unlink('/tmp/installer.sig'); \
        if (!hash_equals(\$signature, \$hash)) { \
            unlink('/tmp/installer.php'); \
            echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
            exit(1); \
        }"

# Install Composer.
RUN php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer \
    && composer --ansi --version --no-interaction \
    && rm -rf /tmp/* /tmp/.htaccess \
    && chmod 777 /composer/cache \
    && echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini"

COPY entrypoint.sh /run/entrypoint.sh

ENTRYPOINT ["/run/entrypoint.sh"]

CMD ["composer"]
