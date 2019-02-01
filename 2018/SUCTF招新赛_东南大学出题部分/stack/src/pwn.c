#include<stdio.h>


void next_door(){
    system("/bin/sh");
}



void main()
{
    setvbuf(stdin, 0, 2, 0);
    setvbuf(stdout, 0, 2, 0);
    puts(" ____  _   _  ____ _____ _____ ");
    puts("/ ___|| | | |/ ___|_   _|  ___|");
    puts("\\___ \\| | | | |     | | | |_   ");
    puts(" ___) | |_| | |___  | | |  _|  ");
    puts("|____/ \\___/ \\____| |_| |_|    ");
    puts("                               ");
	puts("============================");
    char buf[0x18];
    read(0,buf,0x30);
}
