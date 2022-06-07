<?php
session_start();
require('library.php');

//login.phpから受け取った変数を受け取り
if (isset($_SESSION['id']) && isset($_SESSION['name'])){ //値が入っているかの条件式
    $id =$_SESSION['id'];
    $name = $_SESSION['name'];
    $photo = $_SESSION['photo'];
} else { //入っていなければログインフォームに戻される
    header('Location: login.php');
    exit;
}

$db = dbconnect(); //DB接続

//メッセージの投稿
if ($_SERVER['REQUEST_METHOD'] === 'POST') { //ブラウザのリクエストがPOSTだった時
    $message = filter_input(INPUT_POST,'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  //messageの文字処理
    $stmt = $db->prepare('insert into posts (message, member_id) values(?,?)'); //
    if(!$stmt) {
        die($db->error);
    }

    $stmt->bind_param('si', $message, $id);
    $success = $stmt->execute();
    if(!$success) {
        die($db->error);
    }

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ヒトコト</title>

    <link rel="stylesheet" href="style.css"/>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>ヒトコト</h1>
    </div>
    <div id="content">
        <div style="text-align: right"><a href="logout.php">Logout</a></div>
        <form action="" method="post">
            <dl>
                <!-- マイページ -->
                <dt><a href="mypage.php?id=<?php echo h($id); ?>"><?php echo h($name); ?></a> please enter a message...</dt>
                <!-- テキストエリア -->
                <dd>
                    <textarea name="message" cols="50" rows="5"></textarea>
                </dd>
            </dl>
            <div>
                <p style="padding-bottom: 15px;">
                    <input type="submit" value="done"/>
                </p>
            </div>
        </form>


        <?php 
         $stmt = $db->prepare('select p.id, p.member_id, p.message, p.access, m.name, m.picture from posts p, members m where m.id=p.member_id order by id desc'); //sqlのセット
        if (!$stmt) {//エラー処理
            die($db->error);
        }
        $success = $stmt->execute();//sql実行。エラー処理のために変数に挿入
        if (!$success) {//エラー処理
            die($db->error);
        }
        $stmt->bind_result($id, $member_id, $message, $access, $name, $picture); //各変数に値を挿入

        while ($stmt->fetch()): //値がなくなるまで下の処理を実        
        ?>
<!--------------------------------------------- ヒトコトの一覧表示 ------------------------------------------------------------>
        <div class="msg">
            <!-- トプ画表示 -->
            <?php if ($picture): ?> 
                <img src ="member_picture/<?php echo h($picture); ?>" width="48" height="48" alt="" />
            <?php endif; ?>
                <!-- 一言表示 -->
                <p><dt><span class="name"><a><?php echo h($name); ?></a></dt></span>                
                <?php echo h($message); ?>
                </p>
            <p class="day">
                <!-- 最終アクセスの表示 -->
                <a href="view.php?id=<?php echo h($id); ?>">最終アクセス：<?php echo h($access); ?></a>
                <!-- メッセージ削除機能 -->
                <?php if ($_SESSION['id'] === $member_id): ?>
                    [<a href="delete.php?id=<?php echo h($id); ?>" style="color: #F33;">削除</a>]
                    <?php endif; ?>
                </p>
            </div>
        <?php endwhile; ?>
<!--------------------------------------------------------------------------------------------------------------------------->
    </div>
</div>
</body>

</html>