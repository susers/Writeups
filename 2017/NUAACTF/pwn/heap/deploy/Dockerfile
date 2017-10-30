From asuri/ctf-xinetd:latest

# Clean up example file
RUN rm -fr /etc/xinetd.d/*

# Add prerequest files
RUN useradd -U -m heap_secret
ADD heap_secret /home/heap_secret/heap_secret
ADD flag.txt /home/heap_secret/flag.txt
ADD ctf.xinetd /etc/xinetd.d/heap_secret

# Ensure file privileges are correct
RUN chmod 755 /home/heap_secret/heap_secret \
    && chmod 644 /home/heap_secret/flag.txt \
    && chown root:root /home/heap_secret/flag.txt

# Clean up temp files
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV CTF_PORT 20000
ENV TCPDUMP_ENABLE True
EXPOSE 20000
