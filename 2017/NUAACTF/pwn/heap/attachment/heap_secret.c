#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>

int main()
{
  setbuf(stdout, 0);
  printf("Do U know the secret of heap?\n");
  puts("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");
  printf("If you don't know, please read the source code of malloc function!\n");
  puts("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^");
  puts("Begin you travel to heap world!\n");
  char *a = (char *)malloc(0x100);
  char *b = (char *)malloc(0x100000);
  printf("The secret address: %p\n", a);
  read(0, b, 0x111111);
  return 0;
}
