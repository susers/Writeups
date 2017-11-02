# Writeups

整理国内各大CTF赛题

## 索引

- [2017](./2017)

## 题目递交规范

按照`template`文件夹下内容进行递交

```shell
.\TEMPLATE
│  Readme.md--------------|题目描述文档
│  Writeup.md------------|writeup
│
├─attachments------------|题目文件
├─deploy-----------------|online型题目部署脚本
│  ├─docker_for_pwn-------|pwn题示例
│  │  │  Dockerfile
│  │  │  xctf.xinetd
│  │  │
│  │  └─bin
│  │          pwn
│  │          flag
│  │
│  └─docker_for_web-------|web题示例
│      │  Dockerfile
│      │  run.sh
│      │
│      └─bin
│              index.php
│
└─files----------------|writeup包含的图片等文件

```


