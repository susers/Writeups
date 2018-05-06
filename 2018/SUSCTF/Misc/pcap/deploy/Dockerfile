# Pull base image 
FROM ubuntu:trusty
  
MAINTAINER image "malingtao1019@163.com"  
# update source  
RUN echo "deb http://archive.ubuntu.com/ubuntu trusty main universe"> /etc/apt/sources.list  
RUN apt-get update \
	&& apt-get install -y apache2 php5 

COPY src /var/www/html 
RUN chown www-data:www-data /var/www/html -R
COPY httpd-foreground /usr/bin
RUN chmod +x /usr/bin/httpd-foreground
EXPOSE 80
CMD ["httpd-foreground"]
