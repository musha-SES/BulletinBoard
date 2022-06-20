<?php
/*----------------------------------------------*/
// 機能：退会フォーム
// 退会ボタンを押すと確認用アラート後、全てを削除
/*----------------------------------------------*/
session_start();
require('library.php');

    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
        $name = $_SESSION['name'];
    } else {
        echo("Not Found error");
    }
    
    $db = dbConnect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
        if (isset($_SESSION['name']) && isset($_POST['is_delete']) && $_POST['is_delete'] === '1') {
            $stmt = $db->prepare('DELETE m,p,pl FROM members m LEFT OUTER JOIN posts p ON p.id = m.id LEFT OUTER JOIN profile pl ON p.id = m.id WHERE m.id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();

            session_destroy(); 
            header('Location: ./login.php');
            exit; 
        }
    }
?>

<script>
    // 削除するときに確認用アラートを表示
    function EndUser(){
        var EN = confirm('本当に退会してよろしいですか？');
        return EN;
    }
</script>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" Content="IE=edge">
        <meta name="viewport" Content="width=device-width, initial-scale=1.0">
        <title>ヒトコト</title>
        <link rel="stylesheet" href="css\import.css"/>
    </head>

    <body>
        <div id="Wrap">
            <div id="head">
                <a href="index.php"><h1>ヒトコト --退会手続き--</h1></a>
            </div>
            <div id="Content">
                <div class="header">
                    &laquo;<a href="property.php">Property</a>
                </div>
                <div>
                    <dt><h5>退会しますか？</h5></dt>
                    <form method="POST" onsubmit="return EndUser()">
                        <input type="hidden" name="is_delete" value="1">
                        <input type="submit" class="button" value="退会する">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>