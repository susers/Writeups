#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Date    : 2018-03-28 08:30:08
# @Author  : Xu (you@example.org)
# @Link    : https://xuccc.github.io/
# @Version : $Id$

from cpython import get_dict

def input_filter(string):
    ban = "import exec eval pickle os subprocess input sys 02d210cb93c99343245780ac32c124ac".split(" ")
    for i in ban:
        if i in string.lower():
            print "{} has been banned!".format(i)
            return ""
    return string

def delete_type():
    type_dict = get_dict(type)
    del type_dict['__bases__']
    del type_dict['__subclasses__']

def builtins_clear():
    whiteList = "raw_input dir Exception  TypeError  file".split(" ")
    for mod in __builtins__.__dict__.keys():
        if mod not in whiteList:
            del __builtins__.__dict__[mod]

delete_type()
builtins_clear()

def hack(inp):
    if inp == '02d210cb93c99343245780ac32c124ac':
        print file('5c72a1d444cf3121a5d25f2db4147ebb').read()
    else:
        print "Guess error"


while  1:
    inp = raw_input("SUS>> ")
    cmd = input_filter(inp)
    try:
        exec cmd
    except TypeError as e:
        print "hack function needs arg"
    except Exception as e:
        print "Command is not right"

