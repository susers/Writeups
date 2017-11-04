##  DNS 101

##  Tools

##  Steps

根据DNS服务器中信息

```
oot in ~/Desktop/dns λ dig what.is.my.flag.src.edu-info.edu.cn ANY

; <<>> DiG 9.10.3-P4-Debian <<>> what.is.my.flag.src.edu-info.edu.cn ANY
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 9313
;; flags: qr rd ra; QUERY: 1, ANSWER: 3, AUTHORITY: 0, ADDITIONAL: 0

;; QUESTION SECTION:
;what.is.my.flag.src.edu-info.edu.cn. IN        ANY

;; ANSWER SECTION:
what.is.my.flag.src.edu-info.edu.cn. 5 IN RRSIG NSEC 8 6 600 20171201002610 20171031232610 57636 src.edu-info.edu.cn. biNUeZIYhFoKjjf9TSBLZNou/IzuUtwbV8LE4sVOB8vu86Ky6acKfxq3 msG1fLaLwNJo/xxg7CbrcC+r7z/PVOu+aamVoYKYpLzUmJ+EmTyEQV8+ 656Bag5TfGuH7ij+fSUFRtOcqfGU7qz7uSB7FWScqCG2i7DXIjcEdBno K9c=
what.is.my.flag.src.edu-info.edu.cn. 5 IN NSEC  n.flag.src.edu-info.edu.cn. TXT RRSIG NSEC
what.is.my.flag.src.edu-info.edu.cn. 5 IN RRSIG TXT 8 6 1 20171201002443 20171031232443 57636 src.edu-info.edu.cn. Fbr/dibDNPTN2A4mc0aPG0SeDp/PAv90l07pvzq5nyTxTJqHuoocvZm1 +5jT+WItI/pulJrzR6qvrpiVluF/VZVRM0+Hl4xemzz8g4xsNiHIqD8r ykxwxP5Le3RBGsjY3kUBr3q9eBCNaJ1VeTTYinHft8A8G+kaZdsHNCBH Ph0=

...

root in ~/Desktop/dns λ dig n.flag.src.edu-info.edu.cn  ANY         

; <<>> DiG 9.10.3-P4-Debian <<>> n.flag.src.edu-info.edu.cn ANY
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 51467
;; flags: qr rd ra; QUERY: 1, ANSWER: 4, AUTHORITY: 0, ADDITIONAL: 0

;; QUESTION SECTION:
;n.flag.src.edu-info.edu.cn.    IN      ANY

;; ANSWER SECTION:
n.flag.src.edu-info.edu.cn. 5   IN      RRSIG   NSEC 8 6 600 20171201002610 20171031232610 57636 src.edu-info.edu.cn. pbrbVgbzTMrhiz5/AX+zuYhexosdAdR65SLh6VF+gnjDDehcEFiuC6TZ hX0Gy1QrxvGMuWaqkd5TR2/TklhkZti2/w97d4GO41I3TMg2yCiXG4no dmr3nJdim9SdqqeOVohV5zyX8i3Xu71o675UtEjCjTuNsUNJkSDuGDuY zfk=
n.flag.src.edu-info.edu.cn. 5   IN      NSEC    z.flag.src.edu-info.edu.cn. TXT RRSIG NSEC
n.flag.src.edu-info.edu.cn. 5   IN      RRSIG   TXT 8 6 1 20171201002610 20171031232610 57636 src.edu-info.edu.cn. YkqQWk4U2BlD6U67XS8HLu2VP56pe1yHUVv0jAQs5QQGEsY8Zk88sTE5 06UwsedbK/KECLbvuBC4ZfXHY7rbMvz3twlljt3hYemaPP0Jq31yiwrz arGfRP/C3uNaXCfh+wxkLjsYDY3zLrMHLy3xw+cU4/VyRvC9vsTyWc6j uLA=
n.flag.src.edu-info.edu.cn. 5   IN      TXT     "flag-id-[...].flag.src.edu-info.edu.cn. TXT"
```

发现可疑域名`z.flag.src.edu-info.edu.cn`

一路追踪下去发现一连串类似`0riebri3pouprlaxl8miachlupiugoev.zzzzzzz.flag.src.edu-info.edu.cn`的可疑域名

写个脚本一路跟踪到最后

```python
#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Date    : 2017/10/28 15:10
# @Author  : Xu
# @Site    : https://xuccc.github.io/
# @Version : $

from fabric.api import run,env
import re
p = re.compile('NSEC (\w{32})')

env.hosts = ['127.0.0.1']
env.user = 'root'
env.password = 'toor'

def dns(data):
    info =run('dig @dns.src.edu-info.edu.cn  %s.zzzzzzz.flag.src.edu-info.edu.cn any' \
        % data)
    return  re.findall(p,info)[0]


def main():
    data = '3iesplewoefrluwl69ieqo2driefr0u2'
    while True:
        tmp = dns(data)
        data = tmp
        # raw_input('=====================================================')
```

得到flag id

```shell
[127.0.0.1] out: zlar4eslumoafroa8piucrlawouy4est.zzzzzzz.flag.src.edu-info.edu.cn. 600 IN RRSIG	NSEC 8 7 600 20171130040422 20171031034712 57636 src.edu-info.edu.cn. Wf8Ui/VNGycXG1cRtm55haAZfMUkgROSWvqIKtnxlY9hSPjMJK85CWPY 5CmbCjyePJ3zQ9nnOc6g9G6w2Q2Ys/lODCjmnfXKkRRu1GZW0WuT9DVt X14t5zKyMJT+/Zb4slRMuB7pm2T/BZL5zDZieD5jQBWIcFc3HFsueXPc iD8=
[127.0.0.1] out: zlar4eslumoafroa8piucrlawouy4est.zzzzzzz.flag.src.edu-info.edu.cn. 600 IN NSEC flag-id-ztfrneclyudrfq3e6endq5.zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz.zzzzzzz.flag.src.edu-info.edu.cn. TXT RRSIG NSEC
[127.0.0.1] out: zlar4eslumoafroa8piucrlawouy4est.zzzzzzz.flag.src.edu-info.edu.cn. 1 IN	TXT "flag-id-[...].flag.src.edu-info.edu.cn. TXT"
[127.0.0.1] out:
```

得到flag

```shell
flag-id-ztfrneclyudrfq3e6endq5.zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz.zzzzzzz.flag.src.edu-info.edu.cn. 1 IN RRSIG TXT 8 8 1 20171130040836 20171031034712 57636 src.edu-info.edu.cn. mVmG3c+cqwIpSoy0pBcBA29RdYh4xH4CrASK7KlFwi7mB//fqndI5608 P71ykYVgKYmhgDHlfvbhvBzdFperO26BVnD/6Mj/yyIJyVE0uwPqgtoW ZkVJsjQVfCGF9CZua976ulL2DJMEpEuMty0KrM421hahw6MxFdOQCtSk 3PE=
flag-id-ztfrneclyudrfq3e6endq5.zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz.zzzzzzz.flag.src.edu-info.edu.cn. 600 IN RRSIG NSEC 8 8 600 20171201002042 20171031232042 57636 src.edu-info.edu.cn. lbZmKLwsn7D8ZIn3cafKadyug1W5Nz21YyCtvG5eZt6LdohPTeQjGw+Y Jbcx3Fig1x7UUplBnhlPm/3rAEHUgqQa4tB9FgoJBnXXy/INjG05GC53 Z4arQqjEIdYEcZ3AFJFStpSNkcj7CkyOhA0M2DifwlMnVfIh04DLl3GK MR4=
flag-id-ztfrneclyudrfq3e6endq5.zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz.zzzzzzz.flag.src.edu-info.edu.cn. 600 IN NSEC g.zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz.zzzzzzz.flag.src.edu-info.edu.cn. TXT RRSIG NSEC
flag-id-ztfrneclyudrfq3e6endq5.zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz.zzzzzzz.flag.src.edu-info.edu.cn. 1 IN TXT "EIS{DNS_Z0nE_Enum3raTi0N_WiTH0uT_AxFR}"
```
