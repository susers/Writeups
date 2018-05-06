# Docker demo

## Usage

* run `bash RUN.sh`

## Note

**Ensure your send.py and log file privileges are correct so ctfers can't read your challenge token and flag log**

```dockerfile
RUN touch /root/log
RUN chmod 700 /root/*
```