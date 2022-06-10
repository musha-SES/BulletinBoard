<?php
session_start();
require('library.php');

    if (isset($_SESSION['id'])){ //値チェック
        $id =$_SESSION['id'];
        $name = $_SESSION['name'];
    } else { 
        echo("Not Found error");
    }
    
    $db = dbconnect(); //DB接続

    // 退会処理 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ログイン状態で、かつ退会ボタンを押した 
        if (isset($_SESSION['name']) && isset($_POST['is_delete']) && $_POST['is_delete'] === '1') {
            /* 退会 */
            $stmt = $db->prepare('DELETE m,p,pl FROM members m LEFT OUTER JOIN posts p ON p.id = m.id LEFT OUTER JOIN profile pl ON p.id = m.id WHERE m.id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();

            session_destroy(); // セッションを破壊

            header('Location: ./login.php');
            exit; 
        }
    }
?>

<script>
//確認ホップアップ表示
function EndUser(){
    var EN = confirm('本当に退会してよろしいですか？');
    return EN;
}
</script>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ヒトコト</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <div id="wrap">
            <div id="head">
                <h1>ヒトコト --退会手続き--</h1> 
            </div>
            <div id="content">
                <div class="titlebar">
                    <p class="subject">&laquo<a href="property.php">property</a></p>
                </div>
                <div>
                    <dt><h5>退会しますか？</h5></dt>
                    <form method="POST" onsubmit="return EndUser()">
                        <input type="hidden" name="is_delete" value="1">
                        <input type="submit" value="退会する">
                    </form>
                </div>
            </div>
    </div>
</body>
</html>