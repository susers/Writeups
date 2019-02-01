FROM asuri/ctf-xinetd:1.0.1
MAINTAINER Yibai Zhang <xm1994@gmail.com>

# Clean up example file
RUN rm -fr /etc/xinetd.d/*

# Add prerequest files
RUN useradd -U -m ctf && mkdir -p /home/ctf
ADD src/pwn /home/ctf/pwn
ADD src/flag /home/ctf/flag
ADD src/pwn.xinetd /etc/xinetd.d/pwn

# Ensure file privileges are correct
RUN chmod 755 /home/ctf/pwn \
    && chmod 444 /home/ctf/flag \
    && chown -R root:root /home/ctf


ENV CTF_PORT 20000
EXPOSE 20000
