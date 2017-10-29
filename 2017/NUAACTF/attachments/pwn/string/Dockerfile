From asuri/ctf-xinetd:latest

# Clean up example file
RUN rm -fr /etc/xinetd.d/*

# Add prerequest files
RUN useradd -U -m string
ADD string /home/string/string
ADD flag.txt /home/string/flag.txt
ADD ctf.xinetd /etc/xinetd.d/string

# Ensure file privileges are correct
RUN chmod 755 /home/string/string \
    && chmod 644 /home/string/flag.txt \
    && chown root:root /home/string/flag.txt

# Clean up temp files
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV CTF_PORT 20000
ENV TCPDUMP_ENABLE True
EXPOSE 20000
