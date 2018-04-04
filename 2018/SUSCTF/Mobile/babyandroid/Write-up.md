##  Title
BabyAndroid

##  Tools
Jeb / jadx

##  Steps

```
package com.jackgxc.testpro;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View$OnClickListener;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

public class MainActivity extends AppCompatActivity implements View$OnClickListener {
    private String bbbbb;
    private Button btn;
    private EditText input;
    private String inputString;

    public MainActivity() {
        super();
        this.bbbbb = "3267347723651E492C1D7E117C1946325D02432D493B0B62067B";
    }

    public static String a(String arg6) { // hex2int
        char[] v2 = "0123456789ABCDEF".toCharArray();
        StringBuilder v4 = new StringBuilder("");
        byte[] v1 = arg6.getBytes();
        int v3;
        for(v3 = 0; v3 < v1.length; ++v3) {
            v4.append(v2[(v1[v3] & 240) >> 4]);
            v4.append(v2[v1[v3] & 15]);
        }

        return v4.toString().trim();
    }

    public void onClick(View arg6) {
        String v0 = "";
        this.inputString = this.input.getText().toString();
        int v1;
        for(v1 = 0; v1 < this.inputString.length(); ++v1) { // xor
            v0 = v1 == 0 ? v0.concat(String.valueOf(((char)(this.inputString.charAt(v1) ^ 97)))) : v0.concat(String.valueOf(((char)(this.inputString.charAt(v1) ^ v0.charAt(v1 - 1)))));
        }

        if(MainActivity.a(v0).equals(this.bbbbb)) {
            Toast.makeText(((Context)this), "Congraz!", 1).show();
        }
        else {
            Toast.makeText(((Context)this), "failure", 1).show();
        }
    }

    protected void onCreate(Bundle arg2) {
        super.onCreate(arg2);
        this.setContentView(2131296283);
        this.btn = this.findViewById(2131165219);
        this.input = this.findViewById(2131165232);
        this.btn.setOnClickListener(((View$OnClickListener)this));
    }
}
```

和BabyRE一样都是一个连续异或。

[解密脚本](/2018/SUSCTF/Mobile/babyandroid/files_for_writeups/Crack.py)