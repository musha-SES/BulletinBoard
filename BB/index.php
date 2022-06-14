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
            <a href="index.php"><h1>ヒトコト</h1></a>
        </div>
      <div class="wrapper">
            <div id="content">
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
                    <!-- トプ画表示 -->
                        <?php if ($picture): ?>
                            <?php if ($_SESSION['id'] === $member_id): ?>
                                <div class="icon"><a href="mypage.php?id=<?php echo h($_SESSION['id']); ?>"><img src ="member_picture/<?php echo h($picture); ?>"/></a></div>
                                <?php else: ?>
                                <div class="icon"><a href="userPage.php?id=<?php echo h($member_id); ?>"><img src ="member_picture/<?php echo h($picture); ?>"/></a></div>
                            <?php endif; ?>
                        <?php endif; ?>

                    <!-- 投稿者と一言表示 -->
                        <div class="tag">
                            <span><?php echo h($name); ?></span><br>
                            <p><?php echo h($message); ?></p>
                        </div>

                    <!-- 作成日の表示 -->
                        <div class="dayAndDelete">
                            <div class="day"><a href="view.php?id=<?php echo h($id); ?>"><p><?php echo h($created); ?></p></a></div>
                    <!-- メッセージ削除機能 -->
                            <div><?php if ($_SESSION['id'] === $member_id): ?>
                                <form method="POST" action="delete.php?id=<?php echo h($id); ?>" onsubmit="return confirm_test()">
                                    <input type="image" src="images/cash.png"/>
                                </form>
                            <?php endif; ?></div>
                        </div>
                </div>
                <?php endwhile; ?>
<!--------------------------------------------------------------------------------------------------------------------------->
            </div>
        <footer>
            <div class="blockArea">
                <a href="index.php">
                    <div class="footer_tags"><p>Timeline</p></div>
                </a>
                <p1>|</p1>
                <a href="hitokoto.php">
                    <div class="footer_tags"><img src="images/kakiko.png" style="width: 40px;"></div>
                </a>
                <p1>|</p1>
                <a href="mypage.php">
                    <div class="footer_tags"><p>MyPage</p></div>
                </a>
                <!-- <div class="clear"></div> -->
            </div>
        </footer>
       </div>
    </div>
</body>
</html>