From asuri/ctf-xinetd:latest

# Clean up example file
RUN rm -fr /etc/xinetd.d/* && sed -i s/archive.ubuntu.com/mirrors.tuna.tsinghua.edu.cn/g /etc/apt/sources.list \
    && dpkg --add-architecture i386 && apt-get update &&  apt-get install -y libc6:i386 libncurses5:i386 libstdc++6:i386 

# Add prerequest files
RUN useradd -U -m leave
ADD leave /home/leave/leave
ADD flag.txt /home/leave/flag.txt
ADD ctf.xinetd /etc/xinetd.d/leave

# Ensure file privileges are correct
RUN chmod 755 /home/leave/leave \
    && chmod 644 /home/leave/flag.txt \
    && chown root:root /home/leave/flag.txt

# Clean up temp files
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV CTF_PORT 20000
ENV TCPDUMP_ENABLE True
EXPOSE 20000
