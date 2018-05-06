FROM ubuntu:16.04

RUN dpkg --add-architecture i386
RUN sed -i "s/http:\/\/archive.ubuntu.com/http:\/\/mirrors.aliyun.com/g" /etc/apt/sources.list
RUN apt-get update && apt-get -y dist-upgrade
RUN apt-get install -y xinetd libc6:i386 libncurses5:i386 libstdc++6:i386

RUN useradd -m ctf

COPY ./bin/*  /home/ctf/
COPY ./xinetd.conf  /etc/xinetd.d/ctf
COPY ./start.sh  /root/

RUN chmod +x /root/start.sh
# xinted 连接失败信息
RUN echo "Blocked by xinetd" > /etc/banner_fail

RUN chown -R root:ctf /home/ctf &&\
chmod -R 750 /home/ctf &&\
chmod  740 /home/ctf/flag

WORKDIR /home/ctf

CMD ["/root/start.sh"]

EXPOSE 9999
