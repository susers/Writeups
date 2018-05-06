import subprocess
import string
import sys

binary = "MoVfuscation1.354e846facdf6f3d3205a1465d2fd811"

length = 0
result = ""
while 1:
    for i in string.printable:
        p = subprocess.Popen("./%s" % binary, stdin=subprocess.PIPE, stdout=subprocess.PIPE)
        p.stdin.write("1\n")
        p.stdin.write(result+i+"\n")
        output = p.stdout.readlines()[-1]
        if "Congraz~" in output:
            result += "}"
            print result
            sys.exit(0)
        if len(output)-8 > length:
            length += 1
            result += i
            print output
            break