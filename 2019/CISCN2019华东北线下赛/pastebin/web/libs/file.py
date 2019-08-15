from hashlib import md5
import os


def file_put_contents(contents, path, name):
    try:
        p = '{}/{}'.format(path, name)
        if not os.path.exists(os.path.dirname(p)):
            os.makedirs(os.path.dirname(p))
        f = open(p, 'w')
        f.write(contents)
        f.close()
        return True
    except Exception as e:
        print(e)
        return False


def file_get_contents(path, name):
    try:
        f = open('{}/{}'.format(path, name), 'r')
        content = f.read()
        f.close()
        return content
    except:
        return False
