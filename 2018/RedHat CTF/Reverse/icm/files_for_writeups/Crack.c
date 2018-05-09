#include <stdio.h>
#include <string.h>
#include "IDEA.h"

int main(int argc, char **argv)
{  
    uint16_t key[8] = {0x18FE, 0x9C97, 0x0A72, 0x96F5, 0xC2FD, 0xEEAE, 0x1475, 0x92AA};
    uint64_t plaintext[16];
    uint64_t ciphertext[16] = {0xaf91d8edba092825, 0x5a8d815a3e2d0655, 0x21afbcd426617f8d, 0xb44298d5ce7c2442, 0x3faf66cdbd551fd6, 0xcaded815f7d33b3c};

    for (int i = 0; i < 6; i++) {
        idea_decrypt(ciphertext[i], key, &(plaintext[i]));
        printf("%016llx", plaintext[i]);
    }

    printf("\n");
    return 0;  
}

// Output: 666c61677b66353366633164622d623764332d343634332d396234382d3732356631333132396430377ddddddddddddd