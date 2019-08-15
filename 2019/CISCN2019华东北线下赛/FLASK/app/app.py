#!/usr/bin/python
#encoding=utf-8
#Author:She1don
#Env:python2.7
from flask import Flask, session, abort, redirect, url_for, g, request, flash, render_template, current_app
import os, re
import sqlite3
from werkzeug.contrib.cache import SimpleCache
import hashlib
app = Flask(__name__)

app.config['SECRET_KEY'] = hashlib.md5(os.urandom(24)).hexdigest()
cache = SimpleCache()


# app.permanent_session_lifetime = timedelta(minutes=10)
#app
class User():
    def __init__(self, name, mail):
        self.name = name
        self.mail = mail


@app.route('/')
def index():
    if session.has_key('user'):
        success = 0
        hint = "Try to Be Real `admin` "
        return render_template(
            "index.html", user=session['user'], hint=hint, success=success)
    else:
        return redirect(url_for('login'))


#登录
@app.route('/user/login/', methods=['POST', 'GET'])
def login():
    if request.method == "GET":
        return render_template("login.html")
    else:
        try:
            username = request.form['username']
            passwd = request.form['passwd']
        except:
            flash("username and password is required!")
            return render_template("login.html", danger=1)
        if not check_exist(username):
            flash("User Not Register")
            return render_template("login.html", danger=1)
        elif check_passwd(username, hashlib.md5(passwd).hexdigest()):
            session.clear()
            user = get_user(username)
            session['user'] = user
            return redirect(url_for('index'))
        else:
            flash("Password Error")
            return render_template("login.html", danger=1)


#注册
@app.route('/user/register/', methods=['POST', 'GET'])
def register():
    if request.method == "GET":
        return render_template("register.html")
    username = request.form['username']
    mail = request.form['mail']
    passwd = hashlib.md5(request.form['passwd']).hexdigest()
    # except:
    #     flash("username and password is required!")
    #     return render_template("register.html", danger=1)
    #
    # if (len(username) > 32):
    #     flash('Username To Long')
    #     return render_template("register.html", danger=1)
    if not re.match(
            r'^[0-9a-zA-Z_]{0,19}@[0-9a-zA-Z]{1,13}\.[com,cn,net]{1,3}$',
            mail):
        flash("Mail Format Error!")
        return render_template("register.html", danger=1)
    if check_exist(username) or check_mail(mail):
        flash("User or Mail Already Registered")
        return render_template("register.html", danger=1)
    else:
        insert_user(username, passwd, mail)
    flash("Register Successful")
    return render_template("register.html", success=1)


#找回用户
@app.route('/user/reset/', methods=['POST', 'GET'])
def reset():
    if request.method == "GET":
        return render_template("reset.html")
    try:
        username = request.form['username']
    except:
        flash("username required")
        return render_template("reset.html", danger=1)
    if not check_exist(username):
        flash("User Not Register")
        return render_template("reset.html", danger=1)
    else:
        mail = get_mail(username)
        code = hashlib.md5(os.urandom(16)).hexdigest()
        cache.set(mail, code, timeout=60)
        flash("Check Code in Your Mail! Code is valid in 60s.")
        return render_template(
            "reset.html", success=1, code=1, username=username)


#设置新密码
@app.route("/user/update/", methods=['POST'])
def update():
    username = request.form['username']
    passwd = request.form['passwd']
    code = request.form['code']
    if session['code'] == code:
        flash("Update Success!")
        update_passwd(username, hashlib.md5(passwd).hexdigest())
        return render_template("login.html", success=1)
    else:
        flash("Wrong Code!")
        return render_template("reset.html", danger=1)


#登出
@app.route('/user/logout/', methods=['GET'])
def logout():
    session.clear()
    flash("Logout Success!")
    return redirect(url_for("index"))


#查看邮箱
@app.route('/mail/', methods=['GET'])
def mail():
    mail = request.args.get('mail', '')
    if not re.match(
                r'^[0-9a-zA-Z_]{0,19}@[0-9a-zA-Z]{1,13}\.[com,cn,net]{1,3}$',
                mail):
        flash("Mail Format Error")
        return render_template('mail.html',danger=1)
    code = cache.get(mail)
    session['code'] = code
    return render_template('mail.html', code=code)


#更新邮箱
@app.route('/mail/update/', methods=['GET', 'POST'])
def change_mail():
    try:
        if session['user'][1] != 'admin':
            return "Only admin can change mail"
    except:
        return redirect(url_for('login'))
    if request.method == 'GET':
        return render_template("change_mail.html",user=session['user'])
    else:
        name = session['user'][1]
        mail = request.form['mail']
        user = User(name, mail)
        if not re.match(
                r'^[0-9a-zA-Z_]{0,19}@[0-9a-zA-Z]{1,13}\.[com,cn,net]{1,3}$',
                mail):
            template = "Hello {user.name},Error Format Mail:" + user.mail
        else:
            update_mail(name, mail)
            session['user'] = get_user(name)
            template = "Hello {user.name},Update Success"
        return template.format(user=user)


@app.route('/flag', methods=['GET'])
def flag():
    flag = """
    if session['isadmin']:
        return flag
    """
    if session.has_key('isadmin'):
        if session['isadmin'] == 1:
            flag = read_flag()
    return flag


@app.route('/test', methods=['GET'])
def test():
    name = request.args.get('user', '')
    mail = request.args.get('mail', '')
    user = User(name, mail)
    if not re.match(
            r'^[0-9a-zA-Z_]{0,19}@[0-9a-zA-Z]{1,13}\.[com,cn,net]{1,3}$',
            user.mail):
        template = "Hello {user.name},Error For args:" + user.mail
    return template.format(user=user)


@app.errorhandler(404)
def page_not_found(error):
    return "Something Error"


######   API   ######


def read_flag():
    f = open('../flag', 'r')
    return f.read()


def check_exist(username):
    cur = get_db().cursor()
    user = query_db(
        'select * from users where username = ?', [username], one=True)
    if user != None:
        return True
    else:
        return False


def check_mail(mail):
    cur = get_db().cursor()
    user = query_db('select * from users where mail = ?', [mail], one=True)
    if user != None:
        return True
    else:
        return False


def check_passwd(username, passwd):
    cur = get_db().cursor()
    user = query_db(
        'select * from users where username = ? and passwd=?',
        [username, passwd],
        one=True)
    if user != None:
        return True
    else:
        return False


def insert_user(username, passwd, mail):
    cur = get_db().cursor()
    insert_db("insert into users(username,passwd,mail) values(?,?,?)",
              [username, passwd, mail])


def update_passwd(username, passwd):
    cur = get_db().cursor()
    insert_db("update users set passwd=? where username=?", [passwd, username])


def update_mail(username, mail):
    cur = get_db().cursor()
    insert_db("update users set mail=? where username=?", [mail, username])


# def check_token(token):
#     cur = get_db().cursor()
#     user = query_db('select * from users where token = ?', [token], one=True)
#     if user != None:
#         return user
#     else:
#         return None


def get_mail(username):
    cur = get_db().cursor()
    mail = query_db(
        'select mail from users where username = ?', [username], one=True)
    return mail[0]


def get_user(username):
    cur = get_db().cursor()
    user = query_db(
        'select id,username,mail from users where username = ?', [username],
        one=True)
    return user


######   DATABASE   ######
DATABASE = './database.db'


def init_db():
    with app.app_context():
        db = get_db()
        with app.open_resource('schema.sql', mode='r') as f:
            db.cursor().executescript(f.read())
        db.commit()


def get_db():
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = sqlite3.connect(DATABASE)
    return db


@app.teardown_appcontext
def close_connection(exception):
    db = getattr(g, '_database', None)
    if db is not None:
        db.close()


def query_db(query, args=(), one=False):
    cur = get_db().execute(query, args)
    rv = cur.fetchall()
    cur.close()
    return (rv[0] if rv else None) if one else rv


def insert_db(query, args=()):
    cur = get_db().execute(query, args)
    get_db().commit()


if __name__ == '__main__':
    init_db()
    app.run(host="0.0.0.0", port=5000, debug=True)
