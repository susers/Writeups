#include <stdio.h>
#include <stdlib.h>

int main(){
    int target[28] = {0x571a9693,0x23a96034,0x6a943d8c,0x1f9222ed,0x73887c81,0x5c13a257,0x26407522,0x13646a3a,0x2139537f,0x35415c5f,0x321304b7,0x238a8c26,0xd7307f6,0x622d5268,0x7c3d2e04,0x72198f7e,0x7df76af2,0x4e8431aa,0x28650861,0xfd8e3e9,0x196c1f1a,0x5fe8ab3,0x1231495d,0x5359d998,0x35fcfde0,0x3b2d0dd4,0x61113e45,0x314c57b8};
    int tmp[28] = {0};
    int result[28] = {0};
    srand(0x133ED6B);
    for (int i=0;i<28;i++){
        tmp[i] = target[i] - rand();
    }
    for (int i=0;i<28;i++){
        if (i == 0)
            result[0] = tmp[0] ^ 0xff;
        else {
            result[i] = tmp[i] ^ tmp[i-1];
        }
    }
    for (int i=0;i<28;i++)
        printf("%c", result[i]);
    printf("\n");
    return 0;
}