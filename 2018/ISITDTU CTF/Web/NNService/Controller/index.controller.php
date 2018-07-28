<?php
class Action{
    public function __construct($user){
        $this->user=$user;
    }
    public function run(){
		$method = $_GET["a"];
		if(method_exists($this, $method)){
            if(!$this->user->islogin() && !in_array($method,array('login','register','captcha'))){
                header("Location:/index.php/login");
                exit();
            }
			call_user_func(array($this, $method));
		}else{
		    die("<script>alert('Method is not exists.');history.go(-1);</script>");
		}
	}

    public function index(){
        include("templates/index.html");
    }

    public function captcha(){
        require_once("Core/validatecode.class.php");
        $_vc = new ValidateCode();
        $_vc->doimg();
        $_SESSION['code'] = $_vc->getCode();

    }

    public function login(){
        if($this->user->islogin()){
            header("Location:/index.php");
            exit();
        }
        if(isset($_POST['username'])&&isset($_POST["password"])){
            $this->user->login($_POST['username'],$_POST['password']);
            if (!$this->user->islogin()){
                //TODO！report it！
                echo "error! Login failed!";
            }
            else{
                echo "Login success!";
            }
        }
        else{
            include("templates/login.html");
        }
    }

    public function register(){
        if($this->user->islogin()){
            header("Location:/index.php");
            exit();
        }
        if(isset($_POST['username']) and isset($_POST['nickname']) and isset($_POST['password']) and isset($_POST['email']))
        {
            if($this->user->register($_POST['username'],$_POST['nickname'],$_POST['password'],$_POST['email'])===false){
                    //TODO！report it！
                    echo "error! Register failed!";
            }
        	else{
                echo "Register success!";
            }
        }
        else{
            include("templates/register.html");
        }
    }

    public function add(){
        if(isset($_POST['title'])&&isset($_POST['content'])&&$_POST['content']&&$_POST['title']){
            if(is_string($_POST['title']) && is_string($_POST['content'])){
                if($this->user->add($_POST['title'],$_POST['content'])){
                    header("Location:/index.php/view/");
                    exit();
                }
            }
            else{
                //TODO！report it！
                quit('Add failed!');
            }
        }
        else{
            include("templates/add.html");
        }
    }

    public function view(){
        if(isset($_GET['article'])){
            $id=intval($_GET['article']);
            $result=$this->user->getarticle($id);
            if($result==-1){
                quit('You have no access to read this article!');
            }
            else if($result==null){
                //TODO！report it！
                quit('This article is not exists!');
            }
            else{
                if($result[0][2]!="") echo "<h1>".htmlspecialchars($result[0][2], ENT_QUOTES)."</h1>";
                echo htmlspecialchars($result[0][3], ENT_QUOTES);
            }
        }
        else{
            $id=$this->user->getid();
            $this->view=$this->user->getarticle();
            include("templates/view.html");
        }
    }

    public function edit(){
        if(isset($_POST['submit']) and isset($_POST['nickname']) and isset($_POST['email']) and isset($_POST['code'])){
            if($_POST['code']!==$_SESSION['code']){
                quit('validatecode error!');
            }
            if(!$_POST['nickname'] or !$_POST['email']) quit('Something error!');

            if($_POST['nickname']!=$this->user->getnickname())
                if($this->user->edit("nickname",$_POST['nickname']))
                    $_SESSION['user'][2]=$_POST['nickname'];

            if($_POST['email']!=$this->user->getemail())
                if($this->user->edit("email",$_POST['email']))
                    $_SESSION['user'][4]=$_POST['email'];

            if($_FILES['avatar'] and $_FILES["avatar"]["error"] == 0){
                if((($_FILES["avatar"]["type"] == "image/gif") or ($_FILES["avatar"]["type"] == "image/jpeg") or ($_FILES["avatar"]["type"] == "image/png")) and $_FILES['avatar']['size']<65535){
                    $info=getimagesize($_FILES['avatar']['tmp_name']);
                    if(@is_array($info) and array_key_exists('mime',$info)){
                        $type=explode('/',$info['mime'])[1];
						$filepath=$this->user->getuser().".".$type;
                        $filename="uploads/".$filepath;
                        if(is_uploaded_file($_FILES['avatar']['tmp_name'])){
                            $this->user->edit("avatar",array($filepath,$type));
							if(strpos($filepath,"..") !== false)
							{
								die("Hacker, cut please!");
							}
                            else if(move_uploaded_file($_FILES['avatar']['tmp_name'], $filename)){
                                quit_and_refresh('Upload success!','edit');
                            }
                            quit_and_refresh('Success!','edit');
                        }
                    }else {
                        //TODO！report it！
                        quit('Only allow gif/jpeg/png files smaller than 64kb!');
                    }
                }
                else{
                    //TODO！report it！
                    quit('Only allow gif/jpeg/png files smaller than 64kb!');
                }
            }
            quit('Success!');
        }
        else
            include("templates/edit.html");
    }

    public function export(){
        $avatar=$this->user->getavatar();
        if(substr($avatar,0,5)!=="data:"){
            $fileavatar=substr($this->user->getavatar(),1);
			$avatar = "uploads/".$fileavatar;
	

			if(file_exists($avatar) and filesize($avatar)<65535 and strpos($fileavatar,"..")==false){
                $data=file_get_contents($avatar);
                if(!$this->user->updateavatar($data)) quit('Something error!');
            }
            else{
                //TODO！report it！
                $out="Your avatar is invalid, so we reported it"."</p>";
                include("templates/error.html");
				die("<br>");
            }
        }
        $article=$this->user->getarticle();
        $data="";
        for($i=0;$i<count($article);$i++){
            if($i!=count($article)-1){
                $data.=$article[$i][2]."\r\n";
                $data.=$article[$i][3]."\n";
                $data.="----------\n";
            }
            else{
                $data.=$article[$i][2]."\r\n";
                $data.=$article[$i][3]."\n";
            }
        }
        $data.="==========\n";
        $avatar=$this->user->getavatar(1);
        $data.=base64_encode($avatar[1])."\n";
        $data.=$avatar[3];
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");
        header("Content-Length: ".strlen($data));
        header("Content-Disposition: attachment; filename=\"".$this->user->getuser()."\"");
        echo $data;
    }
    public function backup(){
        include("templates/e_and_i.html");
    }

    public function about(){
        include("templates/about.html");
    }
    public function logout(){
        $this->user->logout();
        header("Location:/index.php");
        exit();
    }
}
?>
