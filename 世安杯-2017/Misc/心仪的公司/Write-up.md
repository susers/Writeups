##  Title

##  Tools

##  Steps

- Step 1

`tshark`发现疑似webshell的可以文件`conf1g`

- Step 2

`tcp contains conf1g`过滤很快发现可疑请求`action=file&thefile=E%3A%2Fwamp%2Fwww%2Fwebshell.jpg&doing=downfile&dir=E%3A%2Fwamp%2Fwww%2F&zip_file=E%3A%2Fwamp%2Fwww%2F192.168.1.108.zip&exclude=`

得到flag

```shell
s@.(.N`k.UuUI.D.......o.....c...i.H.m..j...x....t....
?.].b......9.;.....n5...f]$.6..r....0.b.......D,.*.WMa.B.....+a<....bh.............&..<.~8.hgvq;.N..w.....q;.N.v...L.;.6i.*.N..Ls.....q;.N.......i....c...fl4g:{ftop_Is_Waiting_4_y}
```



