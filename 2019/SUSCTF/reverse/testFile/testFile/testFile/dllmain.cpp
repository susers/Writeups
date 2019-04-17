// dllmain.cpp : 定义 DLL 应用程序的入口点。
#include "pch.h"
#include "./Dll1.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

//SUSCTF{!!!LQTgniyey}
BOOL APIENTRY DllMain( HMODULE hModule,
                       DWORD  ul_reason_for_call,
                       LPVOID lpReserved
                     )
{
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
    switch (ul_reason_for_call)
    {
    case DLL_PROCESS_ATTACH:
    case DLL_THREAD_ATTACH:
    case DLL_THREAD_DETACH:
    case DLL_PROCESS_DETACH:
        break;
    }
    return TRUE;
}

