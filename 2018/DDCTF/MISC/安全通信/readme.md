# Title 

>安全通信

## Description

>请通过nc 116.85.48.103 5002答题，mission key是b9ba15b341c847c8beba85273f9b7f90，agent id随意填就可以
```python
#!/usr/bin/env python
import sys
import json
from Crypto.Cipher import AES
from Crypto import Random


def get_padding(rawstr):
    remainder = len(rawstr) % 16
    if remainder != 0:
        return '\x00' * (16 - remainder)
    return ''


def aes_encrypt(key, plaintext):
    plaintext += get_padding(plaintext)
    aes = AES.new(key, AES.MODE_ECB)
    cipher_text = aes.encrypt(plaintext).encode('hex')
    return cipher_text


def generate_hello(key, name, flag):
    message = "Connection for mission: {}, your mission's flag is: {}".format(name, flag)
    return aes_encrypt(key, message)


def get_input():
    return raw_input()


def print_output(message):
    print(message)
    sys.stdout.flush()


def handle():
    print_output("Please enter mission key:")
    mission_key = get_input().rstrip()

    print_output("Please enter your Agent ID to secure communications:")
    agentid = get_input().rstrip()
    rnd = Random.new()
    session_key = rnd.read(16)

    flag = '<secret>'
    print_output(generate_hello(session_key, agentid, flag))
    while True:
        print_output("Please send some messages to be encrypted, 'quit' to exit:")
        msg = get_input().rstrip()
        if msg == 'quit':
            print_output("Bye!")
            break
        enc = aes_encrypt(session_key, msg)
        print_output(enc)


if __name__ == "__main__":
    handle()

```

## Category
Misc

## Deployment
* online
* socat

## Flag


## Score

