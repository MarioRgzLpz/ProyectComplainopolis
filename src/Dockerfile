FROM php:apache

RUN apt-get update -y && apt-get install -y libmariadb-dev

RUN docker-php-ext-install mysqli

COPY /home/mariorl/Desktop/DGIIM/ProyectoTW/src/img /var/www/html

EXPOSE 80
