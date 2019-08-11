<?php
require('dbconnect.php');
session_start();

function h($str){echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');}

if ($_COOKIE['email'] != '') {
    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];
    $_POST['keep'] == 'yes';
}

if(isset($_POST)) {
    // 入力されたEmailとPasswordがレコード通りかどうか、空ではないか確認
    if($_POST['email'] != '' && $_POST['password'] != '') {
        $login = $db->prepare('SELECT * FROM users WHERE email=? AND password=?');
        $login->execute(array($_POST['email'],
        sha1($_POST['password'])
        ));
    $user = $login->fetch();
    
    // もしチェックボックスにチェックが入っていたら
    if($_POST['keep'] == 'yes') {
        // setcookie関数でemailとpasswordを１週間保管
        setcookie('email'.$_POST['email'].time()+60*60*24*7);
        setcookie('password'.$_POST['password'].time()+60*60*24*14);
    }
    
        if($user) {
            // ログインに成功したら
            $_SESSION['id'] = $user['id'];
            $_SESSION['time'] = time();
            
            header('Location: post/index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
<head>
<meta charset="utf-8">
</head>
<body>
    <h2>Login Form</h2>
    <form method='post' action=''>
        <label>Email</label>
        <input type='text' name='email' size='40' maxlength='100' value='<?php h($_POST['email']) ?>'>
        <br>
        <label>Password</label>
        <input type='text' name='password' size='20' maxlength='100' value='<?php h($_POST['password']) ?>'>
        <br>
            <?php if($error['login'] == 'blank'): ?>
            Email and Password are required...<br>
            <?php elseif($error['login'] == 'failed'): ?>
            Your Email or Password is wrong...<br>
            <?php endif; ?>
        <input type='submit' value='login'><br>
        <input type='checkbox' name='keep' value='yes'>Do you wanna keep your infomation?
    </form>
</body>
</html>