#include <stdio.h>
#include <cstring>

#define debuge

using namespace std;

const int MAX = 1e5;
const int mod = 1 << 7;
char s[MAX], a[4], b[4];
int len, tmp, dir;
unsigned long long tmod;

void dbg(int t) {
#ifdef debuge
	printf("%d:\n\t", t);
	for (int i = 0; i < tmp; ++i)
		printf("%4d", s[i]);
	puts("");
#endif
	return ;
}
void change() {
	len = strlen(s), tmp = len + (4 - len % 4) % 4, dir = tmp / 4, tmod = 0;
	dbg(0);
	
	//add some unusable element to alignment up;
	for (int i = len; i < tmp; ++i) 
		s[i] = ((long long)i * i * tmp * len) % mod;
	if (len != tmp)
		len = tmp, s[len] = '\0';

	//change each letter with their position;
	for (int i = 0; i < len; ++i)
		tmod += s[i] * (i+1);
	tmod &= (1 << (tmod & 7)) - 1;
	printf("%d\n", (int)tmod);
	dbg(1);
	for (int i = 0; i < len; ++i) {
		s[i] += tmod * i + ((long long)i+2) * (i+1);
		s[i] &= (((unsigned int)1 << 7) - 1);
	}
	dbg(2);

	//bit move;
	for (int i = 0; i < dir; ++i) {
		for (int j = 0; j < 4; ++j)
			a[j] = s[i + dir * j], b[j] = 0;
		for (int j = 0; j < 4; ++j)
			for (int k = 0; k < 4; ++k)
				b[j] |= (a[(k + j) % 4] & ((unsigned int)3 << (((unsigned int)3 - k) << 1)));
		for (int j = 0; j < 4; ++j)
			s[i + dir * j] = b[j];
	}
	dbg(3);
	return ;
}
int main() {
	while (scanf("%s", s) != EOF) {
		change();
		//output in a reverse order
		for (int i = len-1; i >= 0; --i)
			printf("%4d", s[i]);
		puts("");
	}
	return 0;
}
