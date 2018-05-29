# 安全通信

## Steps
python solve.py
```python 
import socket
mission_key="2acba569d223cf7d6e48dee88378288a"

begin=24

ans=""

while True:
    f=socket.socket(socket.AF_INET,socket.SOCK_STREAM)
    f.connect(("116.85.48.103",5002))
    print f.recv(1024)
    f.send(mission_key+"\n")
    print f.recv(1024)
    f.send('a'*begin+"\n")
    crypt=f.recv(1024)
    data=f.recv(1024)
    print crypt,data,begin,((len(ans)/16)+1)*32
    querystring='{}0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM'
    i=0
    while i<len(querystring):
        f.send(querystring[i]+ans+"\n")
        info=f.recv(1024).split()
        #print querystring[i]
        try:
            data=info[0]
        except:
            continue
        #print info,data,-(((len(ans)/32)+1)*32+1),crypt[-((len(ans)/32)+1)*32-1:-1]
        if(crypt[-((len(ans)/16)+1)*32-1:-1]==data):
            print "mark"
            ans=querystring[i]+ans
            print ans
            begin=begin+1
            break
        data=f.recv(1024)
        #print "[+]"+data
        i=i+1
    print "================data is {} end a loop".format(ans)%                                                                                                 

```
