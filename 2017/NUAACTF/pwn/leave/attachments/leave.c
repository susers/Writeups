#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>

//gcc -fno-stack-protector -z execstack -m32 -o leave leave.c && strip -s leave

char put_buf[1024];
char buf[1024];

char key[200] = "key\n";

int handle_user_input()
{
  return snprintf(put_buf, 0x400u, buf);
}

int echo()
{
  handle_user_input();
  puts(put_buf);
  return 0;
}


void main_func(){

	while ( 1 )
	{
		fgets(buf, 1024, stdin);
		if(strcmp(buf,key)==0){
			printf("_______________\n");
			break;
		}
		echo();
	}
}


int main()
{
	alarm(60);
	setbuf(stdout, 0);

	puts("~~ welcome to nuaa ctf ~~");
	main_func();
    return 0;
}
