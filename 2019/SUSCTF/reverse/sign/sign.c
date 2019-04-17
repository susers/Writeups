#include <stdio.h>
#include <string.h>

int main() {
    char input[80] = {0};

    scanf("%s", input);
    input[79] = 0;

    if (!strcmp(input, "susctf{RwVIphVJg9BFYAEqRYf6mnX38Vy467a9}")) {
        printf("Congraz! You got it!\n");
    } else {
        printf("Nice try~\n");
    }
    
    return 0;
}