<?php

try{
    $db = new PDO('mysql:dbname=twitter;host=127.0.0.1;charset=utf8',
    'root', '4v8h7mus');
    } catch (PDOException $e) {
        echo 'DB接続エラー：' . $e->getMessage();
}
?>