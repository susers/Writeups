#!/usr/bin/python

from Crypto.Util.number import *
from secret import flag, n, t, z



def encrypt_time_capsule(msg, n, t, z):
	m = bytes_to_long(msg)
	l = pow(2, pow(2, t), n)
	c = l ^ z ^ m
	return (c, n, t, z) 

print encrypt_time_capsule(flag, n, t, z)