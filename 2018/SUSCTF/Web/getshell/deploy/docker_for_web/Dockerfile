# Pull base image 
FROM ubuntu:trusty
  
MAINTAINER image "malingtao1019@163.com"  
# update source  
RUN echo "deb http://archive.ubuntu.com/ubuntu trusty main universe"> /etc/apt/sources.list  
RUN apt-get update \
	&& apt-get install -y apache2 php5 

COPY src /var/www/html 
RUN rm /var/www/html/index.html &&\
 chmod a-w  /var/www/html -R &&\
sed -i  "s/disable_functions = /disable_functions = system,popen,shell_exec,exec,passthru,proc_open/g" /etc/php5/apache2/php.ini
COPY httpd-foreground /usr/bin
EXPOSE 80
CMD ["httpd-foreground"]
