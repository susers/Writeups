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