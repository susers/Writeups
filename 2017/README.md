# Writeups

整理国内各大CTF赛题

## 索引

- [SusCTF 萌新赛 2017.9](./SusCTF萌新赛)
- [第四届问鼎杯全国大学生网络信息安全与保密技能大赛 2017.9](./WDCTF-finals)
- [第四届“世安杯”网络安全大赛 2017.10](./世安杯)
- [江苏省天翼杯第六届信息安全技能竞赛初赛 2017.10](./江苏省天翼杯第六届信息安全技能竞赛初赛)

## 题目递交规范

按照`template`文件夹下内容进行递交

```shell
.\TEMPLATE
│  Readme.md--------------|题目描述文档
│  Write-up.md------------|writeup
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
