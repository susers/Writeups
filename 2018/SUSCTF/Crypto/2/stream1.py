from flag import flag
assert flag.startswith("Susctf{")
assert flag.endswith("}")
assert len(flag)==43


def lfsr(R,mask):
    output = (R << 1) & 0xffffff
    i=(R&mask)&0xffffff
    lastbit=0
    while i!=0:
        lastbit^=(i&1)
        i=i>>1
    output^=lastbit
    return (output,lastbit)

R=int(flag[7:-1],2)
mask    =   0b1010011010100

f=open("key","ab")
for i in range(12):
    tmp=0
    for j in range(8):
        (R,out)=lfsr(R,mask)
        tmp=(tmp << 1)^out
    f.write(chr(tmp))
f.close()




#Susctf{11010111001010000001101110111100111}