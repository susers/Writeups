#!/usr/bin/env python3
from flask import request, redirect, make_response, render_template
from libs.crypto import decrypt, encrypt
from libs.file import *
from libs.database import *
from app import app
import json

hint = "exec /readflag"

class ErrExcp(Exception):
    def __init__(self, type, message):
        self.type = type
        self.message = message


class AuthExcp(Exception):
    def __init__(self, type, message):
        self.type = type
        self.message = message

@app.route('/console')
def fixbug():
    return redirect("/")

@app.errorhandler(ErrExcp)
def errExcpHandler(error):
    e = {
        "type": error.type,
        "message": error.message
    }
    return render_template("error.html", error=e)

@app.errorhandler(AuthExcp)
def errAuthHandler(error):
    e = {
        "type": error.type,
        "message": error.message
    }
    return render_template("autherror.html", error=e)


@app.before_request
def before_request():
    request.is_admin = False
    auth = request.cookies.get('auth')
    # print(auth)
    if auth is not None:
        plain = decrypt(auth).decode()
        print(plain)
        if plain == 0:
            raise AuthExcp(500, "invalid auth")

        try:
            j = json.loads(plain)
        except Exception as e:
            print(e)
            raise AuthExcp(500, "invalid json")
        request.is_admin = j["admin"]
        if not j["admin"]:
            raise AuthExcp(500, "Are you the manager?")
    else:
        j = {
            "uid": 100,
            "admin": False
        }
        resp = redirect('/')
        resp.set_cookie("auth", encrypt(json.dumps(j)))
        return resp

@app.route('/')
def root_path():
    resp = redirect('index.php')
    resp.headers['hint'] = hint
    return resp


@app.route('/index.php')
def index():
    resp = redirect('paste.php')
    resp.headers['hint'] = hint
    return resp


@app.route('/view.php')
def view():
    id = request.args.get('id')
    file_path = Paste.query.filter_by(id=id).first()
    if file_path is not None:
        file_path = file_path.filePath
    else:
        file_path = "not_found"
    content = file_get_contents('upload', file_path)
    raise ErrExcp(123,content)
    if not content:
        raise ErrExcp(500, "FILE ERROR")
    return render_template("view.html", content=content)


@app.route('/paste.php', methods=['POST', 'GET'])
def paste():
    if request.method == "GET":
        return render_template("paste.html")
    else:
        content = request.form['code']
        name = request.form['name']
        if ".." in name:
            raise ErrExcp(500, "Hacker?")
        if file_put_contents(content, './upload', name):
            ret = db.engine.execute("insert into paste (filePath) values (\'{}\')".format(name))
            return redirect('/view.php?id={}'.format(ret.lastrowid))
        else:
            raise ErrExcp(500, "FILE IO ERROR")


if __name__ == '__main__':
    db.create_all()
    db.session.add(Paste("hint"))
    db.session.commit()
    app.debug = True
    app.run(host="0.0.0.0")
