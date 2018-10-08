FROM andrzejtracz/php-apache

COPY bin/docker-php-entrypoint /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

COPY . /var/www

RUN chown -R www-data:www-data /var/www
RUN composer install --verbose --prefer-dist --no-interaction

ENTRYPOINT ["docker-php-entrypoint"]

EXPOSE 80

CMD ["apache2-foreground"]
