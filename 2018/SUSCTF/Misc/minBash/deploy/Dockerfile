FROM ubuntu:16.04

RUN sed -i "s/http:\/\/archive.ubuntu.com/http:\/\/mirrors.aliyun.com/g" /etc/apt/sources.list
RUN apt-get update && apt-get -y dist-upgrade
RUN apt-get install -y openssh-server  binutils curl
RUN apt-get install -y python2.7

RUN useradd -m -s /bin/rbash ctf 
RUN echo "ctf:ctf" |chpasswd

COPY ./bin/flag /home/ctf/c8049f64c8080af25f414b15cb6f80c3

RUN chown -R root:ctf /home/* &&\
chmod -R 740  /home/ctf/* &&\
chmod 740 /home/ctf/c8049f64c8080af25f414b15cb6f80c3

RUN mkdir /home/ctf/bin
RUN echo "export PATH=/home/ctf/bin" >> /home/ctf/.bashrc

RUN ln -s /bin/grep /home/ctf/bin
RUN ln -s /usr/bin/strings /home/ctf/bin
RUN ln -s /usr/bin/curl /home/ctf/bin/curl
RUN ln -s /usr/bin/python2.7 /home/ctf/bin/python

RUN echo 'Welcome to SUSCTF,Try to cat the flag in minBash' > /etc/ssh/ssh_banner &&\
echo 'Banner /etc/ssh/ssh_banner' >> /etc/ssh/sshd_config

RUN mkdir /var/run/sshd 
EXPOSE 22

CMD ["/usr/sbin/sshd", "-D"]  
