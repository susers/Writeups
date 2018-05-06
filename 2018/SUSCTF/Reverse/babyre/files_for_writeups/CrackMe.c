#include <stdio.h>

int main(){
    int ans;
    char in[50], out[50];
    int target[50] = {-115, -40, -117, -56, -100, -38, -95, -12, -124, -4, -93, -110, -31, -66, -37, -70, -55, -80, -17, -101, -85, -12, -105, -27, -124, -25, -116, -14, -113};
    printf("Your flag?\n");
    scanf("%s", in);
    for (int i=0;i<50;i++){
        if (in[i] == 0){
            ans = i;
            break;
        }
        if (i == 0){
            out[0] = in[0] ^ 0xde;
        }
        else {
            out[i] = out[i-1] ^ in[i];
        }
    }
    for (int i=0;i<ans;i++){
        if ((target[i] ^ out[i]) != 0){
            printf("You should try harder.\n");
            return 0;
        }
    }
    printf("Congratz.\n");
    return 0;
}