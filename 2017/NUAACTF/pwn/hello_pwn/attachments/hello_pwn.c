#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

struct bof
{
	int input;
	int key;
	
}test;

int hack(){
	system("cat flag.txt");
	return 0;
}

int main()
{
	alarm(60);
	setbuf(stdout, 0);
	puts("~~ welcome to nuaa ctf ~~");
	puts("lets get helloworld for bof");
	read(0,&test.input,16);
	if (test.key==0x6e756161)
	{
		hack();
	}
    return 0;
}
