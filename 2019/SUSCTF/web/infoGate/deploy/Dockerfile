FROM ubuntu:16.04
  
MAINTAINER y4ngyy "591620892yy@gmai.com" 
# update source  
# RUN echo "deb http://archive.ubuntu.com/ubuntu xenial main universe"> /etc/apt/sources.list  
ENV DEBIAN_FRONTEND noninteractive 
COPY sources.list /etc/apt/
RUN apt-get update \
	&& apt-get install -y apache2 php7.0 libapache2-mod-php7.0 mysql-server php7.0-mysql 

COPY src /var/www/html 
COPY web2.sql /tmp/
RUN rm /var/www/html/index.html &&\
 chown www-data:www-data /var/www/html -R && mkdir /var/www/html/Uploads \
&& chmod 666 /var/www/html/Uploads &&\
chown www-data:www-data /var/www/html/Uploads &&\ 
ln -s /var/lib/mysql/mysql.sock /tmp/mysql.sock && \
chown -R mysql:mysql /var/lib/mysql \
&& service mysql start && mysql -e "grant all privileges on *.* to 'root'@'%' identified by 'toor';" && mysql -e "grant all privileges on *.* to 'root'@'localhost' identified by 'toor';" && mysql -u root -ptoor -e "create database web2;" && mysql -u root -ptoor -e "show databases;" && mysql -u root -ptoor web2</tmp/web2.sql && mysql -u root -ptoor -e "show databases;"
COPY httpd-foreground /usr/bin/
RUN chmod a+x /usr/bin/httpd-foreground 
EXPOSE 80
CMD ["httpd-foreground"]
