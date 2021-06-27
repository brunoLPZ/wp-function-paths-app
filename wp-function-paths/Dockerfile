FROM ubuntu:20.04

ENV TZ=Europe/Madrid
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y ca-certificates-java

RUN update-ca-certificates -f

RUN apt install -y openjdk-11-jre

RUN apt-get install -y php-fpm git

RUN apt-get install -y curl cron wget

WORKDIR /opt/

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN php composer.phar require nikic/php-parser
RUN php composer.phar require ramsey/uuid

COPY src/main/resources/entrypoint.sh .
COPY src/main/resources/plugins-cron /etc/cron.d/plugins-cron

RUN chmod 0644 /etc/cron.d/plugins-cron
RUN crontab /etc/cron.d/plugins-cron

COPY target/wp-function-paths-1.0-SNAPSHOT.jar .

ENTRYPOINT ["/bin/bash", "entrypoint.sh"]
