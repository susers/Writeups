from flask import Flask


app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = "sqlite:///./db.db"
app.config['STATIC_FOLDER'] = "static"
if __name__ == '__main__':
    app.debug = True
    app.run()
