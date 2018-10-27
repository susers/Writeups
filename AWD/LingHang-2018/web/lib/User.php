<?php


class User extends base{
    
    private $db;
    function __construct(){
        parent::__construct();
        $this->conn=mysql_connect(DBHOST,DBUSER,DBPASS);
        mysql_select_db(DBNAME);

    }
    
    function Login(){
        if (!empty($_POST['username']) and !empty($_POST['password'])){
            $username=$_POST['username'];
            $password=md5($_POST['password']);
            $sql="select * from users where username='$username' and password='$password'";
            $result = mysql_query($sql,$this->conn);
            $data = array();
              if($result && mysql_num_rows($result)>0){
                $data = mysql_fetch_assoc($result);
                $_SESSION['username']=$username;
                $_SESSION['id']=$data['id'];
                header("Location: ./index.php?c=User&a=home");
            }else{
                exit("password error!");
            }
        }
    }
    
    function Index(){
       
        $this->tp->display("index.tpl");
    }

    function Home(){

        if(isset($_SESSION['id'])){
            $sql="select * from users where id=".$_SESSION['id'].";";
            $result = mysql_query($sql,$this->conn);
            if($result && mysql_num_rows($result)>0){
            $data = mysql_fetch_assoc($result);
            $_SESSION['id'] = $data['id'];
            $_SESSION['birthday'] = $data['birthday'];
            $_SESSION['phonenumber'] = $data['phonenumber'];
            $_SESSION['QQ'] = $data['QQ'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['photo'] = $data['photo'];
            $_SESSION['reward'] = $data['reward'];
            $_SESSION['motto'] = $data['motto'];
            $_SESSION['age'] = $data['age'];
            if($data['sex']==1){
                $_SESSION['sex'] = "女";
            }
            else{
                $_SESSION['sex'] = "男";
            }
            $this->tp->assign("username",$_SESSION['username']);
            //example
            //把所有session的数据放到表格中！
            $this->tp->assign("id",$_SESSION['id']);
            $this->tp->assign("phonenumber",$_SESSION['phonenumber']);
            $this->tp->assign("QQ",$_SESSION['QQ']);
            $this->tp->assign("email",$_SESSION['email']);
            $this->tp->assign("photo",$_SESSION['photo']);
            $this->tp->assign("reward",$_SESSION['reward']);
            $this->tp->assign("motto",$_SESSION['motto']);
            $this->tp->assign("age",$_SESSION['age']);
            $this->tp->assign("sex",$_SESSION['sex']);
            $this->tp->assign("birthday",$_SESSION['birthday']);
            $this->tp->display("home.tpl");
        }else{
            header("location: ./index.php");
        }
      
    }
}
    function register(){
        
        if (!empty($_POST['username']) and !empty($_POST['password']) and !empty($_POST['password']) and !empty($_POST['password']) and !empty($_POST['password'])){
            $username=addslashes($_POST['username']);
            $password=md5($_POST['password']);
            $age=$_POST['age'];
            $sex=$_POST['sex'];
            $sql="select * from users where username='$username'";
            $result = mysql_query($sql,$this->conn);
            if($result && mysql_num_rows($result)>0){
                $this->tp->display("register.tpl");  
                $this->tp->display("error.tpl");     
            }else{
                $sql="insert into users(username,password,age,sex) values('$username','$password','$age','$sex')";
                if (mysql_query($sql)){
                	$this->tp->display("index.tpl");
                    $this->tp->display("success.tpl");            
                }
            }
        }else{
            $this->tp->display("register.tpl"); 
        }    
    }

    function upload(){
        if(isset($_SESSION['id'])){
            include_once __DIR__."/File.php";
            $up=new File();
            $path = $up->save();
            if($path){
                //这里还有一个坑
                $sql="update users set photo='".$path."' where id=".$_SESSION['id'].";";
                if(mysql_query($sql,$this->conn)){
                    $this->tp->assign("photo",$path);
                    $this->tp->display("success.tpl");
                }
                else
                {
                    $this->tp->display("error.tpl");
                }
      
        }else{
            $this->tp->display("error.tpl");
        }
    }
}

    function logout(){
        $_SESSION=array();
        session_destroy();
        header("location: ./index.php");
    }

    function updatepass(){

        if (!empty($_POST['password'])){
            $password=md5($_POST['password']);
            $sql="update users set password='$password' where id='".$_SESSION['id']."';";
            if (mysql_query($sql)){
                $this->tp->display("success.tpl");
            }
        }
        else{
            $this->tp->display("updatepass.tpl");
        }
    }
    function Updateinfo(){
        if(isset($_SESSION['id'])){
        	$user_id=$_SESSION['id'];
            $user_name=addslashes($_POST['user_name']);
            $user_QQ=$_POST['user_QQ'];
            if($_POST['user_sex']=='女'){
            	$user_sex=0;
            }
            else{
            	$user_sex=1;
            }
            $user_age=$_POST['user_age'];
            $user_phone=$_POST['user_phone'];
            $user_email=$_POST['user_email'];
            $user_birth=$_POST['user_birth'];
            $user_reward=$_POST['user_reward'];
            $sql = "update users set username='$user_name',QQ='$user_QQ',sex=$user_sex,age=$user_age,phonenumber='$user_phone',email='$user_email',birthday='$user_birth',reward='$user_reward' where id=$user_id;";
            if (mysql_query($sql)){
                $this->tp->display("success.tpl");
                }
            else{

            	$this->tp->display("error.tpl");
            }
            
        }
    }
    function ping(){
    	$host = $_POST['host'];
        system("ping -c $host");
    }
}
?>