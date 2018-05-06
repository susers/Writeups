##  minBash

##  Tools

- ssh 

##  Steps

```sh
ctf@7ec56ab12af0:~$ ls
-rbash: ls: command not found

ctf@7ec56ab12af0:~$ python
Python 2.7.12 (default, Dec  4 2017, 14:50:18) 
[GCC 5.4.0 20160609] on linux2
Type "help", "copyright", "credits" or "license" for more information.
>>> import os
>>> os.listdir('.')
['.bashrc', '.bash_logout', '.profile', 'bin', 'c8049f64c8080af25f414b15cb6f80c3']
>>> os.path.isfile('c8049f64c8080af25f414b15cb6f80c3')
True
>>> 
ctf@7ec56ab12af0:~$ strings c8049f64c8080af25f414b15cb6f80c3
SUSCTF{e6b729cdf8885b16e7b949e85772e340}

ctf@7ec56ab12af0:~$ grep sus -i c8049f64c8080af25f414b15cb6f80c3
SUSCTF{e6b729cdf8885b16e7b949e85772e340}
ctf@7ec56ab12af0:~$ 

```