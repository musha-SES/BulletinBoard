<?php
session_start();
require('library.php');

if (isset($_SESSION['id'])){ //値チェック
    $id =$_SESSION['id'];
    $name = $_SESSION['name'];
    $photo = $_SESSION['photo'];
    $profile = loadProfile($id);
} else { 
    echo("Not Found error");
}

$db = dbconnect(); //DB接続
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>

    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<!--------------------------------------------- 名前とプロフィール画像の表示 ------------------------------------------------------------>
    <div id="wrap">
        <div id="head">
            <h1>MyPage</h1> 
        </div>
        <div id="content">
            <div class="titlebar">
                <p class="subject"><a href="index.php">Timeline</a></p>
                <p class="date"><a href="logout.php">Logout</a></p><br>
                <p class="property"><a href="property.php">Property</a></p>
            </div>
            <!-- 名前とプロフィール画像の表示 -->
            <div class="profile">
                <h3>[<?php echo h($name); ?>]</h3>
                <?php if ($photo): ?>
                    <img src="member_picture/<?php echo h($photo); ?>" width="150" />
                    <?php endif; ?><br>
                <a><?php echo h($profile); ?></a>
            </div>
            
<!------------------------------------------------------------------------------------------------------------------------------------->
                
<!--------------------------------------------- 自分の投稿した一言を一覧表示 ------------------------------------------------------------>
        <h4>--- hitototo ---</h4>
        <?php //一言データの取得sql
            $sth = $db->prepare('select id, message, member_id, created  from posts where member_id=? order by id desc'); //sqlのセット
                if (!$sth) { //エラー処理
                    die($db->error);
                }
            $sth->bind_param('i',$id);
            $sth->execute();
            $sth->bind_result($pid, $message, $member_id, $created); //各変数に値を挿入
 

        while($sth->fetch()): //値がなくなるまで下の処理を実行 
        ?>
            <div class="msg">
            <!-- 一言表示 -->
            <p><dt><span class="name"><a><?php echo h($name); ?></a></dt></span>                 
                <?php echo h($message); ?></p>
            <p class="day">
                    <!-- 作成日の表示 -->
                    <a href="view.php?id=<?php echo h($pid); ?>">作成日：<?php echo h($created); ?></a>
                    <!-- メッセージ削除機能 -->
                    <?php if ($_SESSION['id'] === $member_id): ?>
                        [<a href="delete.php?id=<?php echo h($pid); ?>" style="color: #F33;">削除</a>]
                    <?php endif; ?>
            </p>
            </div>
        <?php endwhile; ?>
<!------------------------------------------------------------------------------------------------------------------------------------->
        </div>
    </div>
</body>
</html>