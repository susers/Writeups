# Pull base image 
FROM ubuntu

MAINTAINER EVatom "15603401903@163.com"  

# update source  
RUN rm /etc/apt/sources.list
COPY ./sources.list /etc/apt/

# for apt to be nointeractive
ENV DEBIAN_FRONTEND noninteractive
ENV DEBCONF_NONINTERACTIVE_SEEN true
RUN echo "tzdata tzdata/Areas select Europe" > /tmp/preseed.txt; \
    echo "tzdata tzdata/Zones/Europe select Berlin" >> /tmp/preseed.txt; \
    debconf-set-selections /tmp/preseed.txt

# update
RUN apt-get update && apt-get install -y tzdata

#install APM

RUN apt-get install -y apache2 php7.2 libapache2-mod-php7.2

COPY src /var/www/html 
RUN rm /var/www/html/index.html && chown www-data:www-data /var/www/html -R
COPY ./dockered /bin/httpd-foreground
RUN chmod a+x /bin/httpd-foreground
EXPOSE 80
CMD ["httpd-foreground"]
