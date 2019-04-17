#include"./Dll1.h"
#include "pch.h"
#include<stdio.h>
#include<stdlib.h>


extern "C" __declspec(dllexport) int add(int a, int b) {
	return a + b;
}

extern "C" __declspec(dllexport) int sub(int a, int b) {
	return (a - b);
}


extern "C" __declspec(dllexport) void printHello() {
	printf("Hello,world!");
}