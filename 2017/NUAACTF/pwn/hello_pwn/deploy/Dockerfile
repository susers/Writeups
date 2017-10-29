From asuri/ctf-xinetd:latest

# Clean up example file
RUN rm -fr /etc/xinetd.d/*

# Add prerequest files
RUN useradd -U -m hello_pwn
ADD hello_pwn /home/hello_pwn/hello_pwn
ADD flag.txt /flag.txt
ADD ctf.xinetd /etc/xinetd.d/hello_pwn

# Ensure file privileges are correct
RUN chmod 755 /home/hello_pwn/hello_pwn \
    && chmod 644 /flag.txt \
    && chown root:root /flag.txt

# Clean up temp files
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV CTF_PORT 20000
ENV TCPDUMP_ENABLE True

EXPOSE 20000
