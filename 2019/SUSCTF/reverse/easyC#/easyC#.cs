using System;
using System.Security.Cryptography;
using System.Text;

namespace CalculatorApplication
{
    class test
    {
        public static string Sha1(string str)
        {
            var buffer = Encoding.UTF8.GetBytes(str);
            var data = SHA1.Create().ComputeHash(buffer);
            var sb = new StringBuilder();
            foreach (var t in data)
            {
                sb.Append(t.ToString("X2"));
            }
            return sb.ToString();
        }

        static void Main(string[] args)
        {
            string flag1 = Console.ReadLine();
            //5H@l
            if (flag1.Length != 4)
            {
                Console.WriteLine("You failed!!!");
            }
            string enc_flag1 = Sha1('@' + flag1 + '!');
            if (enc_flag1 != "bca8a4be6351503a1f1a04b08ae985eb6ad44f94")
            {
                Console.WriteLine("You get part1!");
            }

            //md5
            string flag2 = "[" + Console.ReadLine() + "]";
            string enc_flag2 = "";
            MD5 md5 = MD5.Create();
            byte[] s = md5.ComputeHash(Encoding.UTF8.GetBytes(flag2));
            for (int i = 0; i < s.Length; i++)
            {
                enc_flag2 = enc_flag2 + s[i].ToString("X2");
            }

            Console.WriteLine(enc_flag2);
            //CMD5查不到：[M#DC%]，输入中间的5个字符
            if (enc_flag2 == "EAD1B36A98531922A10446E6248B06B5")
            {
                Console.WriteLine("You win!");
                Console.WriteLine("SUSCTF{" + flag1 + "-" + flag2 + "}");
            }
        }
    }
}