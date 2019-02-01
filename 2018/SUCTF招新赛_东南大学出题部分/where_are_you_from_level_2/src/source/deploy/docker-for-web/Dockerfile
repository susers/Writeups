# Pull base image 
FROM ubuntu:17.10
  
MAINTAINER image "malingtao1019@163.com"  
# update source  
RUN bash -c "debconf-set-selections <<< 'mysql-server mysql-server/root_password password NAFOASUFNASODFUISAFIUAS'"
RUN bash -c "debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password NAFOASUFNASODFUISAFIUAS'"

RUN apt-get update \
	&& apt-get install -y apt-transport-https  mysql-server apache2 php php-mysql php-curl
COPY database.sql /root/
RUN /etc/init.d/mysql start &&\
    mysql -u root -pNAFOASUFNASODFUISAFIUAS -e "show databases;" &&\
    mysql -u root -pNAFOASUFNASODFUISAFIUAS </root/database.sql &&\
	mysql -u root -pNAFOASUFNASODFUISAFIUAS -e "show databases;"
RUN sed -Ei 's/^(bind-address|log)/#&/' /etc/mysql/my.cnf \
	&& echo 'skip-host-cache\nskip-name-resolve' | awk '{ print } $1 == "[mysqld]" && c == 0 { c = 1; system("cat") }' /etc/mysql/my.cnf > /tmp/my.cnf \
	&& mv /tmp/my.cnf /etc/mysql/my.cnf

COPY src /var/www/html 
RUN rm /var/www/html/index.html &&\
 chown www-data:www-data /var/www/html -R
COPY httpd-foreground /usr/bin
EXPOSE 80
CMD ["httpd-foreground"]
