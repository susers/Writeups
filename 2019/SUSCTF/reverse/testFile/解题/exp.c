#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include<windows.h>


int main(int argc, char** argv) {

	char* enc = "STQ@PC}&)(FZXj`fitkn";
	for (int i = 0; i < strlen(enc); i++) {
		printf("%c", enc[i] ^ i);
	}
	system("pause");
	return 0;
}