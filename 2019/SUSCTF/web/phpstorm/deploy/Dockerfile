FROM ubuntu:xenial
  
MAINTAINER y4ngyy "591620892yy@gmai.com" 
# update source  
# RUN echo "deb http://archive.ubuntu.com/ubuntu xenial main universe"> /etc/apt/sources.list  
COPY sources.list /etc/apt/
RUN apt-get update \
	&& apt-get install -y apache2 php7.0 libapache2-mod-php7.0

COPY src /var/www/html 
RUN rm /var/www/html/index.html &&\
 chown www-data:www-data /var/www/html -R
COPY httpd-foreground /usr/bin/
RUN chmod a+x /usr/bin/httpd-foreground 
EXPOSE 80
CMD ["httpd-foreground"]