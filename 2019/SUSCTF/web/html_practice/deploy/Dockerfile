FROM ubuntu:14.04 

RUN rm /etc/apt/sources.list
COPY ./sources.list /etc/apt/ 
COPY database.sql /root/
RUN apt-get update &&\
    apt-get install -y mysql-server apache2 php5 php5-mysql &&\
    service mysql start &&\
    mysql -e "grant all privileges on *.* to 'root'@'%' identified by 'toor';"&&\
    mysql -e "grant all privileges on *.* to 'root'@'localhost' identified by 'toor';"&&\
    mysql -u root -ptoor -e "show databases;" &&\
    mysql -u root -ptoor </root/database.sql &&\
	mysql -u root -ptoor -e "show databases;"


COPY src /var/www/html 
RUN rm /var/www/html/index.html &&\
 chown www-data:www-data /var/www/html -R
COPY httpd-foreground /usr/bin/
RUN chmod a+x /usr/bin/httpd-foreground
EXPOSE 80
CMD ["httpd-foreground"]
