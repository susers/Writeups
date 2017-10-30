##**Topic** 
web100

##**Steps**
考查 源码泄露，SQL注入

步骤 找到.bak备份文件打开，Ctrl+F搜索flag定位到关键代码

```
$sql = "SELECT `admin` FROM `users` WHERE `username` = '{$_SESSION['user']}' LIMIT 1";
        $res = $db->query($sql);
        $admin = intval($res->fetch_assoc()['admin']);
        if ($admin === 1) {
            echo '<div>Flag: <pre>' . FLAG . '</pre></div>';
```

显然 **$_SESSION['user']** 是注入点，并且可以通过注册任意用户名来控制，然后就可以为所欲为了。 

由于过滤不严格，只要使查询语句返回1就可以爆出flag，参考payload: **me' and 1=0 union select 1#**

flag: **nuaactf{do_!_B_anxious_MY_friend.}**
