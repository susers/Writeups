这题主要漏洞点在

[![](https://p5.ssl.qhimg.com/t015366b1823f02b6db.png)](https://p5.ssl.qhimg.com/t015366b1823f02b6db.png)

那么我们可以利用返回的level1的C2和level2的C2与G爆破出r[0]-r[1]；
再利用level1的C1和level2的C1与K求出M就可以解密了，详见脚本：

```
# -*- coding: utf-8 -*-

def extended_gcd(a, b):
    x, y = 0, 1
    lastx, lasty = 1, 0
    while b:
        a, (q, b) = b, divmod(a, b)
        x, lastx = lastx - q * x, x
        y, lasty = lasty - q * y, y
    return (a, lastx, lasty)

def modinv(a, m):
    g, x, _ = extended_gcd(a, m)
    if g != 1:
        raise Exception('modular inverse does not exist')
    else:
        return x % m

class Point:
    def __init__(self, x, y):
        self.x, self.y = x, y

    def equals(self, p):
        return (self.x == p.x and self.y == p.y)

class ECurve:
    # y^2 = x^3 + ax + b mod p
    def __init__(self, a, b, p):
        self.a, self.b, self.p = a, b, p

    # The method checks if the point is a valid point
    # and satisfies 4a^3 + 27b^2 != 0
    def check(self, p):
        l = (p.y * p.y) % self.p
        r = (p.x * p.x * p.x + self.a * p.x + self.b) % self.p
        c = 4 * self.a * self.a * self.a + 27 * self.b * self.b
        return l == r and c != 0

    # Implements point addition P + Q
    def add(self, p, q):
        r = Point(0, 0)
        if p.equals(r): return q
        if q.equals(r): return p
        # if P = Q
        if p.equals(q):
            if p.y != 0:
                l = ((3 * p.x * p.x + self.a) % self.p * modinv(2 * p.y, self.p)) % self.p
                r.x = (l * l - 2 * p.x) % self.p
                r.y = (l * (p.x - r.x) - p.y) % self.p
        # if P != Q
        else:
            if q.x - p.x != 0:
                l = ((q.y - p.y) % self.p * modinv(q.x - p.x, self.p)) % self.p
                r.x = (l * l - p.x - q.x) % self.p
                r.y = (l * (p.x - r.x) - p.y) % self.p
        return r

    # Implements modular multiplication nP
    def multiply(self, p, n):
        ret = Point(0, 0)
        while n > 0:
            if n & 1 == 1:
                ret = self.add(ret, p)
            p = self.add(p, p)
            n >>= 1
        return ret

G = Point(0x79be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798, 0x483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8)
K = Point(0xe05fc87bcf70996bedd04fefdf862c1a9d1be7c265aeaa01c064b26d885dbb48, 0xb2fc8bd045cc3927b9325dccdfdb0b31524e551bc41640a21578b72bd24d4f95)
flag = 0x666c61677b378bcf71e09c2de9093708ca2ce1770c1e92a4d998ebb303f3fe4ba2b4cd153cfb

C1 = Point(0xa4c7ad80c3786c06b864e227564eef0f62ac8846396bd60022d8f1361bfccd76, 0x4f1b975180cb7bc0d5f9727483a2c473f933db3996fd1b041fcb06885d40ebac)
C2 = Point(0x5b9f0eec2da107db668b2bc448ba8a321355c1e91a1144761a75a9995d4e7c9a, 0x5c4adca18aa1c00eac68d9ea5ba7f859cc3fc838c2758806e4b0c981b0541a36)

C3 = Point(0x91dfb73c4ebd8ec249fa933e4c6ccc6bcabb7c9b5bd3dae313deb7c77aa70820, 0xc5ed9e124105e5f2b6995300905482236074a89839a45c63e48b078de0a857ea)
C4 = Point(0x5f16bb008b865364af1d885efb7d823db081419a4dba8c7437caa4bc794b9d33, 0x785cba0e0774d699f5ec0b316f5754ad08304102fd111f66db9236664de4256b)

C5 = Point(0xa0f115089a833a6133a5512ca43b62e572ad3a7e410a6816fd0478bd4ac233f4, 0xb498d4d0015b76c953be21f5ec538f8f928ac2bdb7b0ab2fe40c671ced524216)
C6 = Point(0x4d70eeb520d304c8f2a3217808af2c425fd4632fad084d81f486248ade59750e, 0x52cb471ca6c46c2b9da2842b1a928c21417587c52fa07213807b961259e0af5b)

a = 0
b = 7
p = 0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F

curve = ECurve(a, b, p)

"""
C2_ = curve.multiply(C2, 2)

CC = curve.add(C2_, Point(C4.x, curve.p - C4.y))

num = 419665

while num > 0:
    if CC.x == curve.multiply(G, num).x:
        print num
        print  CC.y , curve.multiply(G, num).y
        #exit(0)
    num = num - 1
"""
r0_r1 = 419665

C1_ = curve.multiply(C1, 2)

CC = curve.add(C1_, Point(C3.x, curve.p - C3.y))

M = curve.add(CC,Point(curve.multiply(K, r0_r1).x,curve.p - curve.multiply(K, r0_r1).y))

print hex(M.x ^ flag)[2:].replace("L","").decode('hex')

```