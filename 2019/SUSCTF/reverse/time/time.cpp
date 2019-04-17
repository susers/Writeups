#include <stdio.h>
#include <iostream>
#include <assert.h>
#include <string.h>
#include <ctime>

using namespace std;

int x, y, z;
string p_txt, k_txt, s_txt;
char a[33] = "9165do0fdbbz2cb8aiay1f65e6m4qd26";
char msg[1000],cip[36],enc[1000],decy[1000];
int key[1000];

int find_index(char ch)
{
    int x;
    for(int i=0;i<36;i++)
    {
        if(cip[i] == ch)
        {
            x = i;
            i = 36;      
        }
    }
    return x;
}

int encrypt(string s,string k)
{
    int i,l=0,x,y=0;
    for(i=0;i<s.size();i++)
    {
            if((s[i] >47 && s[i]<58) || (s[i] >96 && s[i]<123))
            {
                x = find_index(s[i]);
                enc[i] = cip[(key[y]+x)%36];
                y++;
                if(y>k.size()) y = 0;
            }
            s_txt += enc[i];
    }
    return i;
}

/*
void decrypt(char s[],string k,int j)
{
    int i,l=0,x,z,y=0;
    for(i=0;i<j;i++)
    {
        if((s[i] >47 && s[i]<58) || (s[i] >96 && s[i]<123))
        {
        x = find_index(s[i]);
        z = x - key[y];
        if(z<0)
        {
            z = 36+z;
        }
        decy[i] = cip[z];
            y++;
        if(y>k.size())
        {
            y = 0;
        }
        }
        cout<<decy[i];
    }
    return ;
}
*/

// 9165do0fdbbz2cb8aiay1f65e6m4qd26 secret
// skvdttnmkopfenwunmgbfzvqfbwpfhdr flag
int check_flag(int k) {
    cout << "Input ur plain message [a-z]{32}:" << endl;
    cin >> p_txt;

    // Check Plain Text Length
    if (p_txt.length() != 32) {
        cout << "Nop." << endl;
        return 0;
    }

    // Generate Key
    while (k > 0) {
        k_txt += (char)k % 256;
        k = k >> 8;
    }
    int i,j,f=0;
    for(i=0;i<p_txt.size();i++)
    {
        if((p_txt[i] >47 && p_txt[i]<58) || (p_txt[i] >96 && p_txt[i]<123))
        {
            k_txt.append(p_txt,i,1);
        }
    }

    // Generate Table
    int l=0,x;
    for(i=0;i<10;i++)
    {
        cip[l] = i+48;
        l++;
    }
    for(i=0;i<26;i++)
    {
        cip[l] = i+97;
        l++;
    }
    for(i=0;i<p_txt.size();i++)
    {
        x = find_index(k_txt[i]);
        key[i] = x;
    }

    encrypt(p_txt, k_txt);

    if (!strcmp(a, s_txt.c_str())) {
        cout << "Congratulations~" << endl;
        cout << "Flag is ur input with susctf{}" << endl;
    }
}

int main() {
    time_t now = time(0);
    tm *t = localtime(&now);

    x = t->tm_hour;
    y = t->tm_min;
    z = t->tm_sec;

    if (t->tm_year == 119) {

        if (t->tm_mday ^ 0xde != 223) {
            cout << "No, u cannot do this :(" << endl;
            return 0;
        }

        while ((t->tm_mon+1) * (t->tm_wday+1) == 8) {
            
            assert(x + 5*y - 3*z == -8);
            assert(2*x - 8*y - z == -204);
            assert(7*x + y + 87*z == 3334);

            check_flag((t->tm_year+1900) * (t->tm_mon+1) * t->tm_mday * t->tm_hour * t->tm_min * t->tm_sec);
            return 0;
        }

        cout << "Go out, hacker!" << endl;
    }

    cout << "Try harder~" << endl;

    return 0;
}