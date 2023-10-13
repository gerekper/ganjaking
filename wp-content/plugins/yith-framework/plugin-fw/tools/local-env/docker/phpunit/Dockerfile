FROM phpunit/phpunit:latest

RUN apk update && \
    apk add --no-cache sqlite bash mariadb-client php7-mysqli subversion wget

RUN apk upgrade subversion

ENTRYPOINT []

WORKDIR /var/www/html/wp-content/plugins/yith-plugin-fw-loader/plugin-fw

CMD /bin/true