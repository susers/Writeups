vs2019编写的一个dll文件
testFile中有所有的代码，其实也不多。。。

unsigned char magic[] = {
		0x53,0x54,0x51,0x40,0x50,0x43,0x7d,0x26,
		0x29,0x28,0x46,0x5a,0x58,0x6a,0x60,0x66,
		0x69,0x74,0x6b,0x6e
	};
	printHello();
	char buf[21];
	scanf_s("%s", buf);
	if (strlen(buf) != 20) {
		_exit(0);
	}
	for (int i = 0; i < strlen(buf); i++) {
		if ((buf[i] ^ i )!= magic[i]) {
			i--;
		}
	}
	printf("You win!!!\n");
	system("pause");


关键就是这些