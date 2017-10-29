FROM asuri/ctf-node:8

ADD . /app

RUN cd /app && yarn

RUN mkdir /etc/service/xss/
ADD xss.sh /etc/service/xss/run
RUN chmod +x /etc/service/xss/run

EXPOSE 3000
