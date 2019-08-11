<?php
require('../dbconnect.php');
session_start();

function h($str){echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');}

if(!empty($_POST)) {
    // バリデーション
    if($_POST['name'] == '') {
        $error['name'] = 'blank';
    }
    if($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if($_POST['password'] == '') {
        $error['password'] = 'blank';
    }
    if(strlen($_POST['password']) < 8) {
        $error['password'] = 'underlength';
    }
    // もしemailが重複していたら
    if(empty($error)) {
        // emailレコードの数を数える
        $user = $db->prepare('SELECT COUNT(*) AS cnt FROM users WHERE email=?');
        $user->execute(array($_POST['email']));
        // SQLの結果を取り出し、
        $record = $user->fetch();
        //レコード数をカウントし、０より多ければ…
        if($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }
    // もしエラーが無しだったら
    if(empty($error)) {
        $_SESSION['user'] = $_POST;
        header('Location: confirm.php');
        exit(); // スクリプトの実行を終了
    }
}

if($_REQUEST['action'] == 'back') {
    $_POST = $_SESSION['user'];
}

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
<head>
<meta charset="utf-8">
</head>
<body>
    <h2>Register Form</h2>
    <form method='post' action=''>
        <label>Name</label>
        <input type='text' name='name' size='30' maxlength='100' value='<?php echo htmlspecialchars($_POST['name']) ?>'>
            <?php if($error['name'] == 'blank'): ?>
            Name is required
            <?php endif; ?>
        <br>
        <label>Email</label>
        <input type='text' name='email' size='40' maxlength='100' value='<?php h($_POST['email']) ?>'>
            <?php if($error['email'] == 'blank'): ?>
            Email is required
            <?php elseif($error['email'] == 'duplicate'): ?>
            This Password was already registered.
            <?php endif; ?>
        <br>
        <label>Password</label>
        <input type='text' name='password' size='20' maxlength='100' value='<?php h($_POST['password']) ?>'>
            <?php if($error['password'] == 'blank'): ?>
            Password is required
            <?php elseif($error['password'] == 'underlength'): ?>
            Password is required at least 8 characters
            <?php endif; ?>
        <br>
        <input type='submit' value='confirm'>
    </form>
</body>
</html>