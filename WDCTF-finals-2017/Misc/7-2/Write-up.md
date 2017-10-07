# WDCTF-2017-finals:7-2

## Steps

- Step 1

解压后发现大堆文件名，尝试base64

`ls|tr -d ' '|base64 -d`

- Step 2

发现`<p@<uk'2Pz1KFAN߬r9HSLainidexingzhuang>$Il{arGks!gb|5` 爱你的新装字样，定位到文件`YWluaWRleGluZ3podWFuZw`

- Step 3

cat后得到`00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 74 94 22 42 91 23 {82 42 82 52 63 21 42 22 73 21 }00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00`

键盘密码 + 凯撒得到flag

`wdflag{ylyoselfve}`

