#include<stdio.h>

char flag[]="SUCTF{4rray_ov3rfloW~}";
int array[10]={0};
int a=1;

int main(){
    setvbuf(stdout,0,2,0);
	setvbuf(stdin,0,2,0);
    puts(" ____  _   _  ____ _____ _____ ");
    puts("/ ___|| | | |/ ___|_   _|  ___|");
    puts("\\___ \\| | | | |     | | | |_   ");
    puts(" ___) | |_| | |___  | | |  _|  ");
    puts("|____/ \\___/ \\____| |_| |_|    ");
    puts("                               ");
	puts("============================");
    int index;
    puts("index:");
    scanf("%d",&index);
    puts("value:");
    scanf("%d",&array[index]);
    if(a==0)
        printf(flag);
    return 0;
}