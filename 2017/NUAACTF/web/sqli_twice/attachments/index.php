<?php

define('CTF', true);

// load configure
require_once './config.php';

// define tab colors
global $color;
$color = [
    'success' => '#4CAF50',
    'error' => '#E53935',
];

session_start();

// connect to database
global $db;
$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME, DBPORT);
if (!$db) {
    die('database connect error');
}

function style() {
    echo <<<STYLE
<style>
    .btn {
        display: inline-block;
        padding: 10px 15px;
        background: #2196F3;
        border: none;
        color: #FFF;
        text-decoration: none;
    }
    pre {
        display: inline-block;
        border: 1px solid #DDD;
        background: #F3F3F3;
        margin: 0;
        padding: 10px 15px;
    }
    .tab {
        margin-bottom: 1em;
        padding: 10px 15px;
        color: #FFF;
    }
</style>
STYLE;
}

function tab($msg, $type) {
    global $color;
    echo '<div class="tab" style="background:' . $color[$type] . '">' . $msg . '</div>';
}

function register() {
    global $db;
    style();
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        if (strlen($username) <= 0) {
            tab('用户名不能为空 :(', 'error');
        } else if (strlen($_POST['password']) < 8) {
            tab('密码过短 :(', 'error');
        } else if (!preg_match('/[0-9]/', $_POST['password'])) {
            tab('密码需要包含至少一个英文数字 :(', 'error');
        } else if (!preg_match('/[a-z]/', $_POST['password'])) {
            tab('密码需要包含至少一个小写字母 :(', 'error');
        } else if (!preg_match('/[A-Z]/', $_POST['password'])) {
            tab('密码需要包含至少一个大写字母 :(', 'error');
        } else if (preg_match('/123|abc|qwe|asd|zxc/i', $_POST['password'])) {
            tab('密码尽量不要包含易猜解的串 :(', 'error');
        } else {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            // using prepare to prevent sql injection
            $stmt = $db->prepare('INSERT INTO `users` (`username`, `password`, `admin`) VALUES (?, ?, 0)');
            $stmt->bind_param('ss', $username, $password);
            $stmt->execute();
            if ($stmt->affected_rows === 1) {
                tab('注册成功 :)', 'success');
            } else {
                tab('用户已存在 :(', 'error');
            }
        }
    } else {
        tab('请填写信息 :(', 'error');
    }
}

function login() {
    global $db;
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        // using prepare to prevent sql injection
        $stmt = $db->prepare('SELECT `password` FROM `users` WHERE `username` = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($password_hash_in_db);
        $stmt->fetch();
        if (!password_verify($password, $password_hash_in_db)) {
            tab('用户名或密码错误 :(', 'error');
            style();
        } else {
            $_SESSION['user'] = $username;
            header('Location: index.php?action=info');
            exit();
        }
    } else {
        tab('请填写信息 :(', 'error');
        style();
    }
}

function info() {
    global $db;
    style();
    if (!isset($_SESSION['user'])) {
        tab('请先登录 :(', 'error');
    } else {
        tab("欢迎，{$_SESSION['user']} :)", 'success');
        // there's no user input at all, `$db->query` is safe
        $sql = "SELECT `admin` FROM `users` WHERE `username` = '{$_SESSION['user']}' LIMIT 1";
        $res = $db->query($sql);
        $admin = intval($res->fetch_assoc()['admin']);
        if ($admin === 1) {
            echo '<div>Flag: <pre>' . FLAG . '</pre></div>';
            echo '<br><br><div style="color:red">检测到入侵！即将删除该账户……<br><br>账户已删除，系统将在三秒内注销。</div>';
            echo '<meta http-equiv="refresh" content="3; url=?action=logout">';
            require_once './threat.php';
        } else {
            echo '<p>Flag: 只有管理员才可以看。</p>';
        }
        echo '<p>没有更多内容了……</p>';
        echo '<p><a href="?action=logout" class="btn">注销</a></p>';
        exit();
    }
}

function logout() {
    session_destroy();
    header('Location: index.php');
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'register': register(); break;
        case 'login': login(); break;
        case 'info': info(); break;
        case 'logout': logout(); break;
    }
}

style();

?>
<h3>已有账号？</h3>
<form action="?action=login" method="post">
    <p>
        <label>账号</label>
        <input type="text" name="username">
    </p>
    <p>
        <label>密码</label>
        <input type="password" name="password">
    </p>
    <input type="submit" value="登录" class="btn">
</form>
<h3>没有账号？</h3>
<form action="?action=register" method="post">
    <p>
        <label>账号</label>
        <input type="text" name="username">
    </p>
    <p>
        <label>密码</label>
        <input type="password" name="password">
        <div>* 密码至少需要八位字符，其中需要出现英文数字、大写和小写字母至少一次，且尽量不要包含易猜解的串。</div>
    </p>
    <input type="submit" value="注册" class="btn">
</form>
