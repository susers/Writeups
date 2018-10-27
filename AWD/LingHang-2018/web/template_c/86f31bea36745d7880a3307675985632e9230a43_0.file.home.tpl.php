<?php /* Smarty version 3.1.26, created on 2018-10-20 19:24:47
         compiled from "templates\home.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:97195bcb107f546954_97210155%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '86f31bea36745d7880a3307675985632e9230a43' => 
    array (
      0 => 'templates\\home.tpl',
      1 => 1540034606,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97195bcb107f546954_97210155',
  'variables' => 
  array (
    'photo' => 0,
    'username' => 0,
    'sex' => 0,
    'age' => 0,
    'QQ' => 0,
    'phonenumber' => 0,
    'email' => 0,
    'birthday' => 0,
    'reward' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.26',
  'unifunc' => 'content_5bcb107f59c532_40098393',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5bcb107f59c532_40098393')) {
function content_5bcb107f59c532_40098393 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '97195bcb107f546954_97210155';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>学生会-简历系统</title>
<!-- <meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width">
<meta name="viewport" media="(device-height: 568px)" content="initial-scale=1.0,user-scalable=no,maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black"> -->
<link rel="stylesheet" href="public/css/gongyong.css">
<?php echo '<script'; ?>
 src="public/js/jquery-1.8.3.min.js" type="text/javascript"><?php echo '</script'; ?>
>
</head>
<body>
<!-- head -->
<div class="head">
<span class="head_lf"><a href="#">后台主页</a></span>
<span class="head_lf"><a class="navbar-brand" href="./index.php?c=User&a=updatepass">修改密码</a></span>
<span class="head_rg"><a href="./index.php?c=User&a=logout">login out</a></span>
</div>
<!-- 上传照片 -->
<div class="img" style="width: 28%;position:fixed" align="center">    
    <!-- <label style="left: 0;"><span>*</span>个人照片：</label> -->
    <img src="<?php echo $_smarty_tpl->tpl_vars['photo']->value;?>
" alt="个人照片" style="width: 150px; height: 200px;"/>
    <!-- <input class="serverImg" id="imgName" name="imgName" /> -->
    <form method="post" action="./index.php?c=User&a=upload" enctype="multipart/form-data">
    <div class="form-group">
    <label for="exampleInputFile">File input</label>
    <input type="file" name="pic" id="exampleInputFile">
    <input  class="btn btn-sm btn-primary btn"  type="submit" value="上传"> </input>
    </div>
    </form>
    <span class="ylbut"></span>
    <span style="font-family: Arial, Helvetica, sans-serif;"></span>
</div>
<!-- 注册信息 -->
<form method="POST" action="./index.php?c=User&a=updateinfo">
    <div class="zhuce">
            <div class="text">
                <span>姓名</span>
                <input name="user_name" type="text" placeholder="请输入姓名" value="<?php echo $_smarty_tpl->tpl_vars['username']->value;?>
" class="input" required/>
            </div>
            <div class="text">
                <span>性别</span>
                <input name="user_sex" type="text" placeholder="请输入性别" value="<?php echo $_smarty_tpl->tpl_vars['sex']->value;?>
" class="input" required/>
            </div>
            <div class="text">
                <span>年龄</span>
                <input name="user_age" type="text" placeholder="请输入年龄" value="<?php echo $_smarty_tpl->tpl_vars['age']->value;?>
" class="input">
            </div>
            <div class="text">
                <span>QQ</span>
                <input name="user_QQ" type="text" placeholder="请输入QQ" value="<?php echo $_smarty_tpl->tpl_vars['QQ']->value;?>
" class="input">
            </div>
            <div class="text">
                <span>手机号</span>
                <input name="user_phone" type="text" placeholder="请输入手机号" value="<?php echo $_smarty_tpl->tpl_vars['phonenumber']->value;?>
" class="input">
            </div>
            <div class="text">
                <span>email</span>
                <input name="user_email" type="text" placeholder="请输入eamil" value="<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
" class="input">
            </div>
            <div class="text">
                <span>出生日期</span>
                <input name="user_birth" type="text" placeholder="请输入出生日期" value="<?php echo $_smarty_tpl->tpl_vars['birthday']->value;?>
" class="input">
            </div>
            <div style="text-align:center;" class="text">
                <span>获奖经历</span>
                <textarea rows="8" cols="100" name="user_reward"><?php echo $_smarty_tpl->tpl_vars['reward']->value;?>
</textarea>
                                    <input  style="width:30%;height:35px;color:white;background:#33CCFF;border:#33CCFF" align="right" type="submit" value="提交修改">
            </div>   
        </div>




    </form>
</body>
</html>
<?php }
}
?>