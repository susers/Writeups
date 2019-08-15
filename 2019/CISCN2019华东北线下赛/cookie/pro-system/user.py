# -*- coding: utf-8 -*-
'''
-------------------------------------------------
    File name :    run.py
    Description : 创建用户模型和Cookie模型
    Author :      RGDZ
    Date :    2019/04/30
-------------------------------------------------
    Version : v1.0
    Contact :   rgdz.gzu@qq.com
    License :   (C)Copyright 2018-2019
-------------------------------------------------
'''
from hashlib import md5

# here put the import lib
class User(object):
    def __init__(self,email,username,password):
        self.email = email
        self.username = username
        self.password = md5(password.encode(encoding='utf8')).hexdigest()
        self.phone = None
        self.qqnumber = None
        self.intro = None
    
    def verify_pass(self,password):
        if password and md5(password.encode(encoding='utf8')).hexdigest() == self.password:
            return True
        return None

    @staticmethod
    def modify_info(obj,dict):
        for key in dict:
            if hasattr(obj,key) and dict[key]!='':
                setattr(obj,key,dict[key])
    


class Cookie(object):
    __key = "abcd"
    def __init__(self):
        __key = "abcd"

    @property
    def create(self):
        self.mix_str = (self.username+Cookie.__key).encode(encoding="utf8")
        self.md5_str = self.username+md5(self.mix_str).hexdigest()
        return self.md5_str
    
    @create.setter
    def create(self,username):
        self.username = username
    
    @staticmethod
    def verify(verify_cookie):
        if verify_cookie:
            username = verify_cookie[:-32]
            verify_str = verify_cookie[-32:]
            return md5((username+Cookie.__key).encode(encoding="utf8")).hexdigest()==verify_str
        return None
