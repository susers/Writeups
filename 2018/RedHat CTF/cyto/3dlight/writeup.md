writeup
===


### 3dlight

这题自己也是莫名其妙做出来了，感觉方法可能不科学…
先把得到的密文转回三维列表lights，用ans暂存要还原的三维列表，初始值是2表示还没还原；
首先检查lights中的0，只要有0，自己和与它直接相连的都不会发光；
随后检查有没有8，有就代表它自己和它直接相连的都会发光；
再检查有没有在面上的7，有就代表它自己和它直接相连的都会发光；
最后检查有没有在棱上的6，有就代表它自己和它直接相连的都会发光；
然后就是无科学性地循环排除，简单来说检查已经找到（ans=0或1）并熄灭的灯的周围有没有大于2且没找到（ans = 2）的灯，如果正好等于中间的数值，就说明这些灯都是发光的；
在这些查找中，如果lights小于等于1且对应ans=2，那它肯定不发光并置ans=0；
最后脚本：

```
# coding=utf-8

c = "0303040201040402040202020102040204020002020504020503010406060400050403040607040104040203050604010501030002050502030303020102050404030302020505010502010201040502050302000306060105050102040705020306010105070602030404020508060303040303040606030304060403040504040202040506040006020305060605020504030305060301050302010404030203040302040603010205040608070201020304040607020001030302030403010403030202050502050605040405050307060604060603010604050405070502040303040507040104040404060703000504050406070301010404010306030103040202020504020403020205060301050603020606020105040203050704010203020405070501040202020407050203050503050705030303050305060301050503030202020305050303050502020305050506050103010303050706030203020306070806040303010202060602030205040303020203030404060501000303030301010203030504020206040302030502030503040102050405070302030305060607070405030301020606030304060302020303040505040405020100020101000101010304040101050402030504000105040302040502020504020203060402040502040405030204060201030605030405040102050404040402".decode('hex')

def str2arr(str):
    return [[[ord(str[i*8*8+j*8+k]) for k in xrange(8)] for j in xrange(8)] for i in xrange(8)]

def arr2str(arr):
    ret = ''
    for i in xrange(8):
        for j in xrange(8):
            tmp = ''
            for k in xrange(8):
                tmp += str(arr[i][j][k])
            ret += chr(int(tmp[::-1],2))
    return ret

lights = str2arr(c)
ans = [[[2 for _ in xrange(8)] for _ in xrange(8)] for _ in xrange(8)]
dir = [[0, 0, 1], [0, 0, -1], [0, 1, 0], [0, -1, 0], [1, 0, 0], [-1, 0, 0]]

def check(x, y, z):
    if x < 0 or x > 7 or y < 0 or y > 7 or z < 0 or z > 7:
        return False
    return True

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] == 0:
                ans[i][j][k] = 0
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z):
                        ans[i + x][j + y][k + z] = 0
for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] == 8:
                lights[i][j][k] = lights[i][j][k] - 2
                ans[i][j][k] = 1
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z):
                        lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 3
                        ans[i + x][j + y][k + z] = 1
                        for (x1, y1, z1) in dir:
                            if check(i + x + x1, j + y + y1, k + z + z1):
                                lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] == 7 and ((i == 0 and j != 0 and k !=0) or (i != 0 and j == 0 and k !=0) or (i != 0 and j != 0 and k ==0)):
                lights[i][j][k] = lights[i][j][k] - 2
                ans[i][j][k] = 1
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z):
                        lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 3
                        ans[i + x][j + y][k + z] = 1
                        for (x1, y1, z1) in dir:
                            if check(i + x + x1, j + y + y1, k + z + z1):
                                lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] == 6 and ((i == 0 and j == 0 and k !=0) or (i != 0 and j == 0 and k ==0) or (i == 0 and j != 0 and k ==0)):
                lights[i][j][k] = lights[i][j][k] - 2
                ans[i][j][k] = 1
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z):
                        lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 3
                        ans[i + x][j + y][k + z] = 1
                        for (x1, y1, z1) in dir:
                            if check(i + x + x1, j + y + y1, k + z + z1):
                                lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] <= 1 and ans[i][j][k] == 2:
                ans[i][j][k] = 0


for i in range(8):
    for j in range(8):
        for k in range(8):
            if ans[i][j][k] == 0 and lights[i][j][k] != 0:
                num = 0
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                        num = num + 1
                if num == lights[i][j][k]:
                    for (x, y, z) in dir:
                        if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                            ans[i + x][j + y][k + z] = 1
                            lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 2
                            for (x1, y1, z1) in dir:
                                if check(i + x + x1, j + y + y1, k + z + z1):
                                    lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1
for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] <= 1 and ans[i][j][k] == 2:
                ans[i][j][k] = 0

for i in range(8):
    for j in range(8):
        for k in range(8):
            if ans[i][j][k] == 0 and lights[i][j][k] != 0:
                num = 0
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                        num = num + 1
                if num == lights[i][j][k]:
                    for (x, y, z) in dir:
                        if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                            ans[i + x][j + y][k + z] = 1
                            lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 2
                            for (x1, y1, z1) in dir:
                                if check(i + x + x1, j + y + y1, k + z + z1):
                                    lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] <= 1 and ans[i][j][k] == 2:
                ans[i][j][k] = 0


for i in range(8):
    for j in range(8):
        for k in range(8):
            if ans[i][j][k] == 0 and lights[i][j][k] != 0:
                num = 0
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                        num = num + 1
                if num == lights[i][j][k]:
                    for (x, y, z) in dir:
                        if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                            ans[i + x][j + y][k + z] = 1
                            lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 2
                            for (x1, y1, z1) in dir:
                                if check(i + x + x1, j + y + y1, k + z + z1):
                                    lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] <= 1 and ans[i][j][k] == 2:
                ans[i][j][k] = 0

for i in range(8):
    for j in range(8):
        for k in range(8):
            if ans[i][j][k] == 1 and  lights[i][j][k] != 0:
                num = 0
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                        num = num + 1
                if num == lights[i][j][k]:
                    for (x, y, z) in dir:
                        if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                            ans[i + x][j + y][k + z] = 1
                            lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 2
                            for (x1, y1, z1) in dir:
                                if check(i + x + x1, j + y + y1, k + z + z1):
                                    lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] <= 1 and ans[i][j][k] == 2:
                ans[i][j][k] = 0

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] != 0:
                num = 0
                for (x, y, z) in dir:
                    if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                        num = num + 1
                if num == lights[i][j][k]:
                    for (x, y, z) in dir:
                        if check(i + x, j + y, k + z) and lights[i + x][j + y][k + z] >= 2 and ans[i + x][j + y][k + z] == 2:
                            ans[i + x][j + y][k + z] = 1
                            lights[i + x][j + y][k + z] = lights[i + x][j + y][k + z] - 2
                            for (x1, y1, z1) in dir:
                                if check(i + x + x1, j + y + y1, k + z + z1):
                                    lights[i + x + x1][j + y + y1][k + z + z1] = lights[i + x + x1][j + y + y1][k + z + z1] - 1

for i in range(8):
    for j in range(8):
        for k in range(8):
            if lights[i][j][k] <= 1 and ans[i][j][k] == 2:
                ans[i][j][k] = 0


flag = arr2str(ans)

print ''.join(flag[0::2][i] + flag[-1::-2][i] for i in xrange(32))
```
得到flag:
![t01be58f30697878c7d]($res/t01be58f30697878c7d.png)

