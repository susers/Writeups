import base64

def lfsr(R,mask):
    output = (R << 1) & 0xffffff
    i=(R&mask)&0xffffff
    lastbit=0
    while i!=0:
        lastbit^=(i&1)
        i=i>>1
    output^=lastbit
    return (output,lastbit)

#R=int(flag[5:-1],2)
mask    =   0b1010011010101001100
f = open("key","r")
ba = base64.b64encode(f.read())
print ba
f.close()


for R  in xrange(300000,int("1111111111111111111",2)):
    
    mrhong = ''
    S = R
    for i in range(12):
        tmp=0
        for j in range(8):
            (R,out)=lfsr(R,mask)
            tmp=(tmp << 1)^out
        mrhong += chr(tmp)


    if base64.b64encode(mrhong)==ba:
        print bin(S)
        break