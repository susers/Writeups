FROM phusion/baseimage:0.10.2
MAINTAINER Yibai Zhang <xm1994@gmail.com>

RUN sed -i 's/archive.ubuntu.com/mirrors.aliyun.com/g' /etc/apt/sources.list && \
    sed -i 's/security.ubuntu.com/mirrors.aliyun.com/g' /etc/apt/sources.list && \
    apt-get update && apt-get install -y apache2 libapache2-mod-php php-mysql mariadb-server && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/www/html/*

RUN mkdir -p /etc/service/apache2/ && \
    printf "#!/bin/sh\n\nexec /usr/sbin/apachectl -D FOREGROUND\n" > /etc/service/apache2/run && \
    mkdir -p /etc/service/mysql/ && \
    printf "#!/bin/sh\n\nexec /usr/bin/mysqld_safe\n" > /etc/service/mysql/run && \
    mkdir -p /var/run/mysqld/ && chown mysql:mysql /var/run/mysqld && \
    chmod 700 /etc/service/mysql/run /etc/service/apache2/run

ADD src/html /var/www/html
ADD src/init_sql.sh /tmp/init_sql.sh
ADD src/clean.sh /clean.sh 
ADD src/flag /flag

RUN echo "secure-file-priv=/var/www/" >>/etc/mysql/mariadb.conf.d/50-server.cnf && \
    chmod 444 /flag && \
    echo "*/5 * * * * root bash /clean.sh">>/etc/cron.d/php

RUN chmod +x /tmp/init_sql.sh && bash -c "/tmp/init_sql.sh" && rm /tmp/init_sql.sh

EXPOSE 80
