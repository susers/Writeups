# Blockchain

# Steps
解题思路:利用create_transaction在头区块后生成一条更长的链覆盖原来的链从而覆盖给黑客的交易记录，然后再利用backdoor给超市转10000，获得一个diamound;然后再将现在这条链覆盖，再利用backdoor给超市转10000，又可以获得一个flag.
添加新区块需要满足新区块的hash少于DIFFICULTY，这里使用所有数字组合来爆破。插入的区块中transaction数组为空，意味着不包含任何交易记录，这样既可避免获取私钥。
爆破新区块的脚本为:
```python

def deal(head):
        global session
        print session
        DIFFICULTY = int('00000' + 'f' * 59, 16)
        print DIFFICULTY
        i=0
        while True:
            transferred = create_tx([], [])
            #print head 
            new_block = create_block(str(head), str(i), [])
            #print new_block
            if int(new_block['hash'],16)<DIFFICULTY:
                print json.dumps(new_block)
            i=i+1
            #print new_block
            #print "hash",int(new_block['hash'],16)
        

deal(sys.argv[1])

```
每次运行将想要插入的位置的hash作为参数,即可生成一个没有交易的空区块，当新生成的链长度大于原有链长度即可覆盖原有的链。

