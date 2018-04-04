hexstr = "3267347723651E492C1D7E117C1946325D02432D493B0B62067B"

num = []

for i in range(0, len(hexstr)/2):
    num.append(int(hexstr[i*2:i*2+2], 16))

result = []

for i in range(0, len(num)):
    if i == 0:
        result.append(num[i] ^ 97)
    else:
        result.append(num[i] ^ num[i-1])

text = ""

for i in result:
    text += chr(i)

print text