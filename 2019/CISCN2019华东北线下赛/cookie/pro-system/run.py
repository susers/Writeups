# -*- coding: utf-8 -*-
'''
-------------------------------------------------
    File name :    run.py
    Description : 用于启动 pro-system app
    Author :      RGDZ
    Date :    2019/04/30
-------------------------------------------------
    Version : v1.0
    Contact :   rgdz.gzu@qq.com
    License :   (C)Copyright 2018-2019
-------------------------------------------------
'''


# here put the import lib
from redis import StrictRedis
from flask import Flask, render_template, redirect, session, request, make_response, url_for, abort
from user import *
import pickle

app = Flask(__name__)
redis = StrictRedis(host='127.0.0.1',port=6379,db=0,password='chocolate')

@app.route('/')
def index():
    cookie = request.cookies.get("Cookie")
    if cookie and redis.exists(cookie):
        return redirect(url_for("home"))
    return redirect(url_for("login"))

@app.route('/login/',methods=['GET','POST'])
def login():
    if request.method != 'GET':
        username = request.form.get('username')
        password = request.form.get('password')
        cookie = Cookie()
        cookie.create = username
        cookie = cookie.create
        try:
            if redis.exists(cookie):
                user = pickle.loads(redis.get(cookie))
                if user.verify_pass(password):
                    resp = make_response(redirect(url_for('home')))
                    resp.set_cookie('Cookie',cookie)
                    return resp
        except:
            abort(500)
    return render_template("login.html")

@app.route('/register/',methods=['GET','POST'])
def register():
    if request.method != 'GET':
        email = request.form.get('email')
        username = request.form.get('username')
        password = request.form.get('password')
        user = User(email,username,password)
        cookie = Cookie()
        cookie.create = username
        cookie = cookie.create
        try:
            if not redis.exists(cookie):
                redis.set(cookie,pickle.dumps(user))
                resp = make_response(redirect(url_for('home')))
                resp.set_cookie("Cookie",cookie)
                return resp
        except:
            abort(500)
    return render_template("register.html")
        

@app.route('/home/',methods=['GET','POST'])
def home():
    cookie = request.cookies.get('Cookie')
    try:
        if Cookie.verify(cookie) and redis.exists(cookie):
            user = redis.get(cookie)
            user = pickle.loads(user)
            if request.method != "GET":
                formlist = request.form.to_dict()
                User.modify_info(user,formlist)
                redis.set(cookie,pickle.dumps(user))
                return render_template("home.html",user=user)
            return render_template("home.html",user=user)
    except:
        return abort(500)
    return redirect(url_for("login"))
    
@app.route('/logout/')
def logout():
    resp = make_response(redirect(url_for('login')))
    resp.set_cookie('Cookie','')
    return resp

@app.errorhandler(500)
def error(e):
    return render_template("error.html")

if __name__ == "__main__":
    app.run(
        debug=True,
        port=8080,
        host="0.0.0.0"
        )