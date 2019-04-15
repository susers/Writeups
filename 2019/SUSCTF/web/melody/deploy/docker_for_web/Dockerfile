#Dockerfile
FROM phusion/baseimage:0.10.1
MAINTAINER Yibai Zhang <xm1994@outlook.com>

RUN sed -i 's/archive.ubuntu.com/mirrors.aliyun.com/g' /etc/apt/sources.list &&\
    sed -i 's/security.ubuntu.com/mirrors.aliyun.com/g' /etc/apt/sources.list &&\
    add-apt-repository ppa:webupd8team/java && apt-get update 
RUN echo yes |apt-get install -y oracle-java8-installer --assume-yes &&\
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/www/html/*

RUN apt-get install -y tomcat                                                 
COPY server.jar /root
COPY flag /flag
COPY httpd-foreground /bin
EXPOSE 8080
CMD ["httpd-foreground"]
