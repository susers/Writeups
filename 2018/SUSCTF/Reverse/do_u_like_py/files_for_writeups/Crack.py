c_txt = "7a44765262592d0f177f6e7457194f64531051531e177676"

# hex2int 
data3 = []

for i in range(0, len(c_txt)/2):
    data3.append(int(c_txt[2*i]+c_txt[2*i+1], 16))

print data3

# 第三阶段，二进制栅栏

data2 = [[0] * 4 for i in range(0, 6)]

for i in range(0, 6):
    for j in range(0, 4):
        data2[i][j] = data3[j*6+i]

print data2

data1 = [[0] * 4 for i in range(0, 6)]

for i in range(0, 6):
    data1[i][0] = (data2[i][0] & 0b11000000) + (data2[i][1] & 0b00000011) + (data2[i][2] & 0b00001100) + (data2[i][3] & 0b00110000)
    data1[i][1] = (data2[i][0] & 0b00110000) + (data2[i][1] & 0b11000000) + (data2[i][2] & 0b00000011) + (data2[i][3] & 0b00001100)
    data1[i][2] = (data2[i][0] & 0b00001100) + (data2[i][1] & 0b00110000) + (data2[i][2] & 0b11000000) + (data2[i][3] & 0b00000011)
    data1[i][3] = (data2[i][0] & 0b00000011) + (data2[i][1] & 0b00001100) + (data2[i][2] & 0b00110000) + (data2[i][3] & 0b11000000)

print data1

data0 = [0] * 24

for i in range(0, 6):
    for j in range(0, 4):
        data0[j*6+i] = data1[i][j]

print data0

# 第二阶段，偏移，偏移量未知，由于flag格式已知，故可以用已知的某位计算偏移量，然后解算全部内容

txt = ""

for i in range(0, 127):
    tmp = ord("U")+i*1+3*2
    tmp = tmp & ((1 << 7) - 1)
    if tmp == data0[1]:
        tmod = i
        print tmod

for j in range(0, 24):
    for i in range(32, 128):
        tmp = i + tmod * j + (j+2) * (j+1)
        tmp = tmp & 0b1111111
        if tmp == data0[j]:
            txt += chr(i)

print txt

# 第一阶段在flag长度为24时无效果