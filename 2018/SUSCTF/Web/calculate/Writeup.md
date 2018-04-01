# Title
calculate
# Tools
* python
# exp
```python
import requests
import time 
from bs4 import BeautifulSoup as bs

sess=requests.session()
url="http://127.0.0.1:8089"
c=sess.get(url).content
b=bs(c,"lxml")
ans=""
for i in b.find_all('div'):
    ans=ans+i.text
res=eval(ans[:-1])
while True:
    try:
        time.sleep(1)
        c=sess.post(url,{"ans":res}).content
        b=bs(c,"lxml")
        ans=""
        for i in b.find_all('div'):
            ans=ans+i.text
        res=eval(ans[:-1])
    except:
        print c
        break
    
```
