<?php
require('../dbconnect.php');
session_start();

$id = $_REQUEST['id'];

$posts = $db->prepare('SELECT * FROM posts WHERE id=?');
$posts->execute(array($id));
$post = $posts->fetch();

if($post['user_id'] == $_SESSION['id']) {
    $del = $db->prepare('DELETE FROM posts WHERE id=?');
    $del->execute(array($id));
}

header('Location: index.php');
exit();
?>