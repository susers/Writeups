FROM phusion/baseimage:0.10.2
MAINTAINER Yibai Zhang <xm1994@gmail.com>

RUN sed -i 's/archive.ubuntu.com/mirrors.aliyun.com/g' /etc/apt/sources.list && \
    sed -i 's/security.ubuntu.com/mirrors.aliyun.com/g' /etc/apt/sources.list && \
    apt-get update && apt-get install -y apache2 libapache2-mod-php  && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/www/html/*

RUN mkdir -p /etc/service/apache2/ && \
    printf "#!/bin/sh\n\nexec /usr/sbin/apachectl -D FOREGROUND\n" > /etc/service/apache2/run && \
    chmod 700 /etc/service/apache2/run &&\
    ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load &&\
    sed -i 's/AllowOverride[ \t]*None/AllowOverride All/g' /etc/apache2/apache2.conf &&\
    sed -i 's/disable_functions = /disable_functions = system,popen,shell_exec,exec,passthru,proc_open,ignore_user_abort/g' /etc/php/7.0/apache2/php.ini &&\
    echo 'open_basedir="/var/www/html:/tmp"\nupload_tmp_dir=/tmp\npost_max_size = 8M'>>/etc/php/7.0/apache2/php.ini &&\
    echo '*/5 * * * * root rm -rf /var/www/html/sandbox/* && touch /var/www/html/sandbox/index.html'>>/etc/cron.d/php

ADD src/000-default.conf /etc/apache2/sites-enabled/000-default.conf
ADD src/html /var/www/html
ADD src/flag /var/www/html/flag_is_h3r3_12bb53a20599af19760293df4c62639c.php 

RUN chmod a-w /var/www/html -R &&\ 
    chmod -R 777 /var/www/html/sandbox 

EXPOSE 80
