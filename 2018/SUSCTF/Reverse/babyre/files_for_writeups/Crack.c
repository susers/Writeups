#include <stdio.h>

int main(){
    int ans;
    char in[50] = {-115, -40, -117, -56, -100, -38, -95, -12, -124, -4, -93, -110, -31, -66, -37, -70, -55, -80, -17, -101, -85, -12, -105, -27, -124, -25, -116, -14, -113}, out[50];
    for (int i=0;i<50;i++){
        if (in[i] == 0){
            ans = i;
            break;
        }
        if (i == 0){
            out[0] = in[0] ^ 0xde;
        }
        else {
            out[i] = in[i-1] ^ in[i];
        }
    }
    for (int i=0;i<ans;i++){
        printf("%c", out[i]);
    }
    printf("\n");
    return 0;
}