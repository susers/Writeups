FROM ubuntu:16.04

RUN dpkg --add-architecture i386
RUN sed -i "s/http:\/\/archive.ubuntu.com/http:\/\/mirrors.aliyun.com/g" /etc/apt/sources.list
RUN apt-get update && apt-get -y dist-upgrade
RUN apt-get install -y xinetd libc6:i386 libncurses5:i386 libstdc++6:i386  socat
RUN apt-get install -y python2.7 python-pip

RUN useradd -m ctf

COPY ./bin/*  /home/ctf/


# xinted 连接失败信息
RUN echo "Blocked by xinetd" > /etc/banner_fail

RUN chown -R root:ctf /home/ctf &&\
chmod -R 750 /home/ctf &&\
chmod  740 /home/ctf/5c72a1d444cf3121a5d25f2db4147ebb


WORKDIR /home/ctf

CMD ["socat", "TCP4-LISTEN:9999,fork", "EXEC:\"python -u /home/ctf/sandbox.py\""]

EXPOSE 9999
