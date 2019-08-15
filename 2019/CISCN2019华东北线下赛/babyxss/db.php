<?php
$servername = "localhost";
$username = "root";
$password = "ciscn2019-sc0de";
$dbname = "ciscn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}


function getSecret($userid,$conn)
{
  
  $sql = "SELECT * FROM `users` WHERE `id` = ".$userid;
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    // 输出数据
    while($row = $result->fetch_assoc()) {
      return $row['username'].' : '.$row['password'];
    }
  } 
  else 
  {
    return "sorry,we don't find this user!";
  }
}


function login($username,$pass,$conn){
    $sql="SELECT id FROM users WHERE username=? and password=?";
    $id=0;
    $mysqli_stmt=$conn->prepare($sql);

    $mysqli_stmt->bind_param('ss',$username,$pass);

    if($mysqli_stmt->execute()){
        $mysqli_stmt->store_result();

        $mysqli_stmt->bind_result($id);
        while ($mysqli_stmt->fetch()) {
            return $id;
        }
    }
    return 0;
}

function register($username,$pass,$conn){

    $conn->set_charset('utf8');
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $mysqli_stmt=$conn->prepare($sql);
    $mysqli_stmt->bind_param('ss',$username,$pass);


    if($mysqli_stmt->execute()){
        // TODO: repair
//        echo $mysqli_stmt->insert_id;
        $conn->close();
        return True;

    }else{
//        echo $mysqli_stmt->error;
        $conn->close();
        return False;

    }

}

//register("5am3","123456",$conn);
//echo login("5am3","1234a56",$conn);


?>