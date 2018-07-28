<?php
class User{
    protected $db;
    protected $islogin;
    protected $username;
    protected $nickname;
    protected $email;
    protected $id;

    function __construct($db){
        $this->db=$db;
        $this->islogin=0;
        if( !isset( $_SESSION ) ) session_start();
        if( isset( $_SESSION['user'] )){
            $this->islogin=1;
            $this->id = $_SESSION['user'][0];
            $this->username = $_SESSION['user'][1];
            $this->nickname = $_SESSION['user'][2];
            $this->email = $_SESSION['user'][4];
        }
    }
    function islogin(){
        return $this->islogin;
    }
    function getuser(){
    	if ($this->islogin) return $this->username;
    	else return null;
    }
    function getnickname(){
    	if ($this->islogin) return $this->nickname;
    	else return null;
    }
    function getemail(){
    	if ($this->islogin) return $this->email;
    	else return null;
    }
    function getid(){
    	if ($this->islogin) return $this->id;
    	else return null;
    }
    function getavatar($raw=0){
        if ($this->islogin){
            $r=$this->db->One("avatar",array("user_id"=>$this->id),array("*"));
            if($raw==1){
                if($r){
                    if($r[1]) return $r;
                    else{
                        $r[1]=file_get_contents($r[3]);
                        return $r;
                    }
                }
                else return array('',file_get_contents("/uploads/0.jpg"),$this->id,"0.jpg",'image/jpeg');
            }
            if($r){
                if($r[1]) return "data:".$r[4].";".base64_encode($r[1]);
                else return "/".$r[3];
            }
            else return "0.jpg";
        }
    	else return null;
    }
    function getarticle($id=-1){
        if($id==-1){
            $result=$this->db->All("articles",array("user_id"=>$this->id),array("*"),array("id desc"));
            if(count($result)===0){
                return null;
            }
            else{
                return $result;
            }
        }
        else{
            $id=intval($id);
            $result[]=$this->db->One("articles",array("id"=>$id),array("*"));
            if(count($result)===0){
                return null;
            }
            else{
                if($this->id==1 or $this->id==$result[0][1]){
                    return $result;
                }
                else{
                    return -1;
                }
            }
        }
    }

    function login($username,$password){

        if(!is_string($username) or !$username or !filter($username)) return false;
		$username=addslashes($username);
        $passhash=md5($password);
    	if($r=$this->db->One("users",array("username" => "'$username'","password" => "'$passhash'"))){
            $_SESSION['user']=$r;
            $this->islogin=1;
            $this->id = $_SESSION['user'][0];
            $this->username = $_SESSION['user'][1];
            $this->nickname = $_SESSION['user'][2];
            $this->email = $_SESSION['user'][4];
    		return true;
        }
    	else
    		return false;
    }
    function register($username,$nickname,$password,$email){
        if(!is_string($username) or !$username or !filter($username)) return false;
        if(!is_string($nickname) or !$nickname or !filter($nickname)) return false;
        if(!is_string($password) or !$password) return false;
        if(!is_string($email) or !$email or !filter($email)) return false;
        if(!preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|([\"].+[\"]))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i',$email)) return false;
    	if ($this->db->One("users",array("username" => "'$username'"))) return false;
		$username=addslashes($username);
		$nickname=addslashes($nickname);
        $email=daddslashes($email);
        $passhash=md5($password);
		return $this->db->Insert("users",array("'$username'","'$nickname'","'$passhash'","'$email'"));
    }

    function logout(){
    	unset($_SESSION['user']);
    }

    function edit($feild,$value){
        if($feild=="email"){
            if(!is_string($value) or !$value or !filter($value)) return false;
            if(!preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|([\"].+[\"]))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i',$value)) return false;
            $value=daddslashes($value);
            return $this->db->Update("users",array("id"=>$this->id),array("email"=>"'$value'"));
        }
        if($feild=="nickname"){
            if(!is_string($value) or !$value or !filter($value)) return false;
            if(!preg_match('/^[a-zA-Z0-9_]+$/i',$value)) return false;
            return $this->db->Update("users",array("id"=>$this->id),array("nickname"=>"'$value'"));
        }
        if($feild=="avatar"){
            return $this->db->Insert("avatar",array("''",$this->id,"'$value[0]'","'$value[1]'"));
        }
    }
    function updateavatar($data){
        $data=daddslashes($data);
        return $this->db->Update("avatar",array("user_id"=>$this->id),array('data'=>"'$data'"));
    }
    function add($title,$content){
        $title=daddslashes($title);
        $content=daddslashes($title);
        return $this->db->Insert("articles",array($this->id,"'$title'","'$content'"));
    }

}

?>
