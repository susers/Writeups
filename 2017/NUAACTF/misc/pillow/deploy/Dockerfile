FROM python:3.6

RUN set -x \
    && mv /etc/apt/sources.list /etc/apt/sources.list.bak \
    && echo "deb http://mirrors.ustc.edu.cn/debian stable main contrib non-free" > /etc/apt/sources.list \
    && echo "deb http://mirrors.ustc.edu.cn/debian stable-proposed-updates main contrib non-free" >> /etc/apt/sources.list \
    && apt-get update \
    && apt-get install -y wget netcat \
    && mkdir ~/.pip/ \
    && echo "[global]" > ~/.pip/pip.conf \
    && echo "index-url = https://pypi.mirrors.ustc.edu.cn/simple" >> ~/.pip/pip.conf \
    && pip install flask Pillow

# local

ARG GS_URL=ghostscript-9.21-linux-x86_64.tgz
ADD $GS_URL /tmp/
RUN mkdir -p /opt/ghostscript \
    && mv /tmp/ghostscript-9.21-linux-x86_64/gs-921-linux-x86_64 /usr/local/bin/gs

# remote

# ARG GS_URL=https://github.com/ArtifexSoftware/ghostpdl-downloads/releases/download/gs921/ghostscript-9.21-linux-x86_64.tgz
# ADD $GS_URL /tmp/gs.tgz
# RUN mkdir -p /opt/ghostscript \
#     && tar zxf /tmp/gs.tgz -C /opt/ghostscript --strip-components=1 \
#     && mv /opt/ghostscript/gs-921-linux-x86_64 /usr/local/bin/gs

ARG PY_SRC=src/
ADD $PY_SRC /app/
RUN useradd -U -m pillow && chmod 644 /app/flag.txt

WORKDIR /app/
EXPOSE 8000
USER pillow

CMD [ "sh", "-c", "python app.py" ]
