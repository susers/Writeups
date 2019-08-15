from app import app
from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy(app)  #


class Paste(db.Model):
    __table__name = 'paste'
    id = db.Column(db.Integer, primary_key=True)
    filePath = db.Column(db.String(200),index=True)

    def __init__(self,path):
        self.filePath = path

    def __repr__(self):
        return self.filePath

    def __str__(self):
        return self.filePath
