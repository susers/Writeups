#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Date    : 2017-10-08 10:38:43
# @Author  : Xu (you@example.org)
# @Link    : https://xuccc.github.io/
# @Version : $Id$

import os

for i in range(100000):
    old = str(i).zfill(5)
    new = old[::-1]
    if i != 0:
        if i * 4 == int(new):
            print int(new)/i,new,i
            print "key is %i" % int(new)
            exit
