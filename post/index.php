<?php
require('../dbconnect.php');
session_start();

function h($str){echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');}

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();
    
    $users = $db->prepare('SELECT * FROM users WHERE id = ?');
    $users->execute(array($_SESSION['id']));
    $user = $users->fetch();
} else {
    header('Location: login.php');
    exit();
}
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $fileName = $_FILES['photo']['name'];
        // バリデーション
        if(!empty($fileName)) {
            $extension = substr($fileName, -4);
            if($extension != '.jpg' && $extension != '.gif' && $extension != '.png' && $extension != 'jpeg') {
                $error['image'] = 'type';
            }
            if($_FILES['photo']['size'] > 40000){
                $error['photo'] = 'size';
            }
        }
        if(strlen($_POST['message']) >= 140) {
            $error['message'] = 'overlength';
        }
        if(empty($error)){
            $photo = date('YmdHis'). $_FILES['photo']['name'];
            $storeDir = '../photos/';
            $name = $_FILES['photo']['tmp_name'];
            
            move_uploaded_file($name,$storeDir.$photo);
            
            $message = $db->prepare('INSERT INTO posts SET user_id=?, message=?, reply_post_id=? , photo=? , created=NOW()');
            $message->execute(array(
                $user['id'],
                $_POST['message'],
                $_POST['reply_post_id'],
                $photo
            ));
            header('Location: index.php');
            exit();
        }
    }
}

// postsテーブルの取得
$posts = $db->query('SELECT user.name, post.* FROM users user, posts post WHERE user.id=post.user_id ORDER BY post.created DESC');

//reply
if(isset($_REQUEST['reply'])) {
    // reply=?に当てはまるpostsのレコードを取得
    $rep = $db->prepare('SELECT user.name, post.* FROM users user, posts post WHERE user.id=post.user_id AND post.id=? ORDER BY post.created DESC');
    $rep->execute(array($_REQUEST['reply']));
    $table = $rep->fetch();
    $message = '[Reply for '. $table['name']. ']';
}
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <button><a href='logout.php'><p>Logout</p></a></button>
    <form action='' method='post' enctype='multipart/form-data'>
        Send <?php h($user['name']) ?>'s Message in 140 characters
        <br>
        <textarea name='message' rows='10' cols='47'><?php h($message); ?></textarea>
        <input type='hidden' name='reply_post_id' value='<?php h($_REQUEST['reply']); ?>'>
        <br>
        <?php if($error['message'] == 'overlength'): ?>Message isn't allowed over 140 characters<br><?php endif; ?>
        <?php if($error['photo']=='size'):?>Uploaded file is over sizing<br><?php endif; ?>
        <input type='submit' value="post">
        <input type='file' name='photo'>
    </form>

    <?php  foreach($posts as $post): ?>
    <div style='border-bottom:solid 1px black;'>
        <?php h($post['name']); ?>:
        <?php h($post['message']); ?>
        (<?php h($post['created']); ?>)
        <input type="hidden" name="MAX_FILE_SIZE" value="400000" /> <!--over FileSize-->
        <?php if($post['photo']): ?>
        <img src='../photos/<?php h($post['photo']) ?>' style='height:100px;'>
        <?php endif; ?>
        <a href='index.php?reply=<?php h($post['id']) ?>'>[Reply]</a>
            <?php if($_SESSION['id'] == $post['user_id']): ?>
                <a href='delete.php?id=<?php h($post['id']) ?>'>[Delete]</a>
            <?php endif; ?>
        <br>
    </div>
    <?php endforeach; ?>

</body>
</html>