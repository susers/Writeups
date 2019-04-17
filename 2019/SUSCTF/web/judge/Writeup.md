# Title
judge
# Tools
* python
# exp
```python
import requests
from lxml import etree
import time

url = "http://211.65.197.117:15000/"
session = requests.Session()


while True:
    response = session.get(url).text
    calc = etree.HTML(response).xpath("//form/div/text()")
    s=calc[0]
    s=s.split('=')
    s[0]=eval(s[0])
    s[1]=eval(s[1])
    result=(s[0]==s[1])
    if(result):
        result="true"
    else :
        result="false"
    print(result)
    data = {
        "answer": result
    }
    time.sleep(1)

    print(data)
    req = session.post(url, data=data)
    print(req.text)
```
