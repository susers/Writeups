# include <stdlib.h>
# include <string.h>
# include <stdio.h>
# include <time.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <sys/mman.h>
# define SECRET1 0x44
# define SECRET2 0x55

// 出题思路：首先放一个栈溢出，但是存在canary，相当于不能用，然后藏一个很长的逻辑在里面，其中再故事的互动过程中存在一个printf。反正就是互动多一点，藏得省一点，假的漏洞有几个

char* dragon = "                                                 .~)>>\n"
"                                               .~))))>>>\n"
"                                             .~))>>             ___\\\n"
"                                           .~))>>)))>>      .-~))>>\\\n"
"                                         .~)))))>>       .-~))>>)>   \n"
"                                       .~)))>>))))>>  .-~)>>)>       \n"
"                   )                 .~))>>))))>>  .-~)))))>>)>\n"
"                ( )@@*)             //)>))))))  .-~))))>>)>\n"
"              ).@(@@               //))>>))) .-~))>>)))))>>)>\n"
"            (( @.@).              //))))) .-~)>>)))))>>)>\n"
"          ))  )@@*.@@ )          //)>))) //))))))>>))))>>)>\n"
"       ((  ((@@@.@@             |/))))) //)))))>>)))>>)>\n"
"      )) @@*. )@@ )   (\\_(\\-\b  |))>)) //)))>>)))))))>>)>\n"
"    (( @@@(.@(@ .    _/`-`  ~|b |>))) //)>>)))))))>>)>\n"
"     )* @@@ )@*     (@) (@)  /\b|))) //))))))>>))))>>\n"
"   (( @. )@( @ .   _/       /  \b)) //))>>)))))>>>_._\n"
"    )@@ (@@*)@@.  (6,   6) / ^  \b)//))))))>>)))>>   ~~-.\n"
" ( @jgs@@. @@@.*@_ ~^~^~, /\\  ^  \b/)>>))))>>      _.     `,\n"
"  ((@@ @@@*.(@@ .   \\^^^/' (  ^   \b)))>>        .'         `,\n"
"   ((@@).*@@ )@ )    `-'   ((   ^  ~)_          /             `,\n"
"     (@@. (@@ ).           (((   ^    `\\        |               `.\n"
"       (*.@*              / ((((        \\        \\      .         `.\n"
"                         /   (((((  \\    \\    _.-~\\     Y,         ;\n"
"                        /   / (((((( \\    \\.-~   _.`\" _.-~`,       ;\n"
"                       /   /   `(((((()    )    (((((~      `,     ;\n"
"                     _/  _/      `\"\"\"/   /'                  ;     ;\n"
"                 _.-~_.-~           /  /'                _.-~   _.'\n"
"               ((((~~              / /'              _.-~ __.--~\n"
"                                  ((((          __.-~ _.-~\n"
"                                              .'   .~~\n"
"                                              :    ,'\n"
;

void welcome(){

      printf("Welcome to Dragon Games!\n");
      puts(dragon);  
}
void BadEnd1(){
  int num, dis;
  printf("You go right, suddenly, a big hole appear front you!\n");
  printf("where you will go?!left(0) or right(1)?!:\n");
  srand((unsigned int)time(NULL));
  while(1){
    num = (rand()%2);
    scanf("%d", &dis);
    if(dis!=num)
      break;
    printf("You escape it!but another hole appear!\n");
    printf("where you will go?!left(0) or right(1)?!:\n");
  }
  printf("YOU ARE DEAD\n");
  exit(0);
}

void atHotel(){

  char choice[5];
  printf(" This is a famous but quite unusual inn. The air is fresh and the\n");
  printf("marble-tiled ground is clean. Few rowdy guests can be seen, and the\n");
  printf("furniture looks undamaged by brawls, which are very common in other pubs\n");
  printf("all around the world. The decoration looks extremely valuable and would fit\n");
  printf("into a palace, but in this city it's quite ordinary. In the middle of the\n");
  printf("room are velvet covered chairs and benches, which surround large oaken\n");
  printf("tables. A large sign is fixed to the northern wall behind a wooden bar. In\n");
  printf("one corner you notice a fireplace.\n");
  printf("There are two obvious exits: east, up.\n");
  printf("But strange thing is ,no one there.\n");

  printf("So, where you will go?east or up?:\n");
  while(1){
    scanf("%s", choice);
  // because we have canary, we just not get length
    if(!strcmp(choice, "east")||!strcmp(choice, "east")){
      break;
    }
    printf("hei! I'm secious!\n");
    printf("So, where you will go?:\n");
  }
  if(!strcmp(choice, "east")){
    return ;
  }
  else if(!strcmp(choice, "up")){
    BadEnd1();
  }
  else{
    printf("YOU KNOW WHAT YOU DO?\n");
    exit(0);
  }
}

void findHole(){
  int num;
  char wish[100];  
  long int addr = 0;

  printf("You travel a short distance east.That's odd, anyone disappear suddenly\n");
  printf(", what happend?! You just travel , and find another hole\n");
  printf("You recall, a big black hole will suckk you into it! Know what should you do?\n");
  printf("go into there(1), or leave(0)?:\n");
  scanf("%d", &num);
  if(num!=1)
    return;
  printf("A voice heard in your mind\n");
  printf("'Give me an address'\n");
  scanf("%ld", &addr);
  printf("And, you wish is:\n");
  scanf("%s", wish);
  printf("Your wish is\n");
  printf(wish);
  printf("I hear it, I hear it....\n");
}

void meetDragon(int *secret){
  printf("Ahu!!!!!!!!!!!!!!!!A Dragon has appeared!!\n");
  printf("Dragon say: HaHa! you were supposed to have a normal\n");
  printf("RPG game, but I have changed it! you have no weapon and \n");
  printf("skill! you could not defeat me !\n");
  printf("That's sound terrible! you meet final boss!but you level is ONE!\n");
  if(secret[0] == secret[1]){
    printf("Wizard: I will help you! USE YOU SPELL\n");
    char *buf = mmap(NULL, 0x1000, PROT_EXEC | PROT_READ | PROT_WRITE, MAP_ANONYMOUS | MAP_SHARED, -1, 0);
    read(0, buf, 0x100);
    ((void (*)(void *))buf)(NULL);
  }

}

void beginStory(int* secret){

  char name[12];

  printf("What should your character's name be:\n");
  scanf("%s", name);
  if(strlen(name) > 12){
    printf("Hei! What's up!\n");
    return ;
  }
  printf("Creating a new player.\n");
  
  // stage one, the black hole
  atHotel();

  // escape dangerous, go into golden hole(at here, you should use printf fomat to change value)
  findHole();

  // meet dragon(if printf format success or something, it)
  meetDragon(secret);

}

int main(int argc, char *argv[])
{
      setbuf(stdout, 0);
      alarm(60);
      char user_input[100];
      int *secret;
      long int_input = 0;

      // first, give welcome infomation
      welcome();


      /* The secret value is stored on the heap */
      secret = (int *) malloc(2*sizeof(int));
      /* getting the secret */
      secret[0] = SECRET1; secret[1] = SECRET2;
      printf("we are wizard, we will give you hand, you can not defeat dragon by yourself ...\n");
      printf("we will tell you two secret ...\n");
      printf("secret[0] is %x\n", &secret[0]);
      printf("secret[1] is %x\n", &secret[1]);
      printf("do not tell anyone \n");

      // then, begin story :)
      beginStory(secret);

      printf("The End.....Really?\n");
      return 0;
}
