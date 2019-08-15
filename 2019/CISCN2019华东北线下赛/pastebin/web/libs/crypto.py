from hashlib import md5
from base64 import b64decode
from base64 import b64encode
from Crypto import Random
from Crypto.Cipher import AES
import codecs
import re


# Padding for the input string --not
# related to encryption itself.
BLOCK_SIZE = 16  # Bytes

key = "PAsteB1n1sG0od11"


def pad(s):
    return s + (16 - len(s) % 16) * chr(16 - len(s) % 16)


def unpad(s):
    t = str(codecs.encode(s, 'hex'))
    exe = re.findall('..', t)
    padding = int(exe[-1], 16)
    exe = exe[::-1]

    if padding == 0 or padding > 16:
        return 0

    for i in range(padding):
        if int(exe[i], 16) != padding:
            return 0
    return s[:-ord(s[len(s) - 1:])]


def encrypt(msg):
    iv = b"cbcISsoGOODddddd"
    raw = pad(msg)
    cipher = AES.new(key, AES.MODE_CBC, iv)
    return b64encode(iv+cipher.encrypt(raw))


def decrypt(enc):
    enc = b64decode(enc)
    iv = enc[:16]
    enc = enc[16:]
    decipher = AES.new(key, AES.MODE_CBC, iv)
    return unpad(decipher.decrypt(enc))
