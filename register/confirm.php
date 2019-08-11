<?php
require('../dbconnect.php');
session_start();

function h($str){echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');}

if(!isset($_SESSION['user'])) {
    header('Location: register.php');
    exit();
}

if(!empty($_POST)) {
    $state = $db->prepare('INSERT INTO users SET name=?, email=?, password=?, created=NOW()');
    echo $ret = $state->execute(array(
        $_SESSION['user']['name'],
        $_SESSION['user']['email'],
        sha1($_SESSION['user']['password']),
        ));
    unset($_SESSION['user']);
    
    header('Location: complete.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <form method='post' action=''>
        <input type='hidden' name='action' value='submit'>
        <label for='name'>Name:</label>
        <?php h($_SESSION['user']['name']); p?>
        <br>
        <label for='email'>Email:</label>
        <?php h($_SESSION['user']['email']); p?>
        <br>
        <label for='password'>Password:</label>
        <?php h($_SESSION['user']['password']); p?>
        <br>
        <input type='submit' value='register'>
    </form>
        <button><a href='register.php?action=back'><p>Back</p></a></button>
</body>
</html>