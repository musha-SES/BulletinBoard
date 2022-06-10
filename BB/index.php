<?php
session_start();
require('library.php');

//値の有無チェック
if (isset($_SESSION['id']) && isset($_SESSION['name'])){ 
    $id =$_SESSION['id'];
    $name = $_SESSION['name'];
} else { //入っていなければログインフォームに戻される
    header('Location: login.php');
    exit;
}

$db = dbconnect(); //DB接続

//メッセージの投稿
if ($_SERVER['REQUEST_METHOD'] === 'POST') { //ブラウザのリクエストがPOSTだった時
    $message = filter_input(INPUT_POST,'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  //messageの文字処理
    $stmt = $db->prepare('insert into posts (message, id) values(?,?)'); //
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
<script>
function confirm_test() {
    var select = confirm("このヒトコトを本当に削除してよろしいですか？");
    return select;
}
</script>

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
                    <p class="date"><a href="logout.php" class="example" style="font-weight: bold;">Logout</a>&raquo</p><br>
                <form action="" method="post">
                    <dl>
                        <!-- マイページ -->
                        <dt><a href="mypage.php?id=<?php echo h($id); ?>" style="font-weight: bold;"><?php echo h($name); ?></a> please enter a message...</dt>
                        <!-- テキストエリア -->
                        <dd><textarea name="message" cols="50" rows="5"></textarea></dd>
                    </dl>
                    <div>
                        <p style="padding-bottom: 15px;"><input type="submit" value="done"/></p>
                    </div>
                </form>

                <?php 
                    $stmt = $db->prepare('select p.hitokoto_id, p.id, p.message, p.created, m.name, m.picture from posts p, members m where m.id=p.id order by hitokoto_id desc'); //sqlのセット
                        if (!$stmt) {//エラー処理
                            die($db->error);
                    }
                    $success = $stmt->execute();//sql実行。エラー処理のために変数に挿入
                        if (!$success) {//エラー処理
                            die($db->error);
                    }
                    $stmt->bind_result($id, $member_id, $message, $created, $name, $picture); //各変数に値を挿入

                while ($stmt->fetch()): //以下ループ処理 ?>
<!--------------------------------------------- ヒトコトの一覧表示 ------------------------------------------------------------>
                <div class="msg">
                    <div class="cow">
                        <!-- トプ画表示 -->
                        <?php if ($picture): ?> 
                            <p class="icon-circle"><img src ="member_picture/<?php echo h($picture); ?>"/></p>
                        <?php endif; ?>
                    </div>     
                        <!-- 投稿者と一言表示 -->
                        <?php if ($_SESSION['id'] === $member_id): ?>
                            <a href="mypage.php?id=<?php echo h($_SESSION['id']); ?>"style="font-weight: bold;"><?php echo h($name); ?></a>
                            <br><?php echo h($message); ?>
                        <?php else : ?>
                            <p><dt><a href="userPage.php?id=<?php echo h($member_id); ?>" style="font-weight: bold;"><?php echo h($name); ?></a>
                        </dt><?php echo h($message); ?></p>
                        <?php endif; ?>

                        <!-- 最終アクセスの表示 -->
                        <div class="day">
                            <a href="view.php?id=<?php echo h($id); ?>"><?php echo h($created); ?></a>
                        </div>
                        <!-- メッセージ削除機能 -->
                        <?php if ($_SESSION['id'] === $member_id): ?>
                            <div class="delete">
                                <form method="POST" action="delete.php?id=<?php echo h($id); ?>" onsubmit="return confirm_test()">
                                    <input type="submit" value="削除"/>
                                </form>
                            </div>
                            <?php endif; ?>
                        
                </div>
                <?php endwhile; ?>
            </div>
<!--------------------------------------------------------------------------------------------------------------------------->
        </div>
    </div>
</body>
</html>