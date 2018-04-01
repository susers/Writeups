# Pull base image 
FROM ubuntu:xenial
  
MAINTAINER image "malingtao1019@163.com"  
# update source  
#COPY sources.list /etc/apt/sources.list

#RUN echo "deb http://archive.ubuntu.com/ubuntu trusty main universe"> /etc/apt/sources.list  
RUN  apt-get update \
	&& apt-get install -y apache2 php libapache2-mod-php7.0 xvfb gtk2-engines-pixbuf xfonts-100dpi x11-xkb-utils xfonts-100dpi xfonts-75dpi xfonts-scalable xfonts-cyrillic phantomjs

COPY src /var/www/html 
COPY scripts /var/scripts


RUN chown www-data:www-data /var/www/html -R &&\
 chmod 777 /var/www/html

COPY httpd-foreground /usr/bin
EXPOSE 80
CMD ["httpd-foreground"]
