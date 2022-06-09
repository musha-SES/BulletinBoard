<?php
session_start();
require('library.php');

//値の有無チェック
if (isset($_SESSION['id']) && isset($_SESSION['name'])){ 
    $id =$_SESSION['id'];
    $name = $_SESSION['name'];
} else {
    header('Location: login.php');
    exit;
}

// IDチェック
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if(!$id){
    header('Location: index.php');
    exit();
}

$db = dbconnect(); //DB接続
changeTheLog($id); // 最終アクセスの更新
?>
<script>
//確認ポップアップの表示
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
                <div class="titlebar">
                    <p class="subject">&laquo<a href="index.php">Timeline</a></p>
                    <p class="date"><a href="logout.php">Logout</a>&raquo</p><br>
                    <p class="mypage"><a href="mypage.php">Userpage</a>&raquo</p>
                </div>

                <?php //SQL実行
                    $stmt = $db->prepare('select p.hitokoto_id, p.id, p.message, p.created, m.name, m.picture from posts p, members m where p.hitokoto_id=? and m.id=p.id order by id desc');
                        if (!$stmt) {
                            die($db->error);
                    }
                    $stmt->bind_param('i', $id);
                    $success = $stmt->execute();
                        if (!$success) {
                            die($db->error);
                    }
                    $stmt->bind_result($id, $member_id, $message, $created, $name, $picture);
                if ($stmt->fetch()): ?>
<!------------------------------------------------------- ヒトコトの表示 ------------------------------------------------------------>
                        <div class="msg">
                            <!-- トプ画表示 -->
                            <?php if ($picture): ?> 
                                <img src ="member_picture/<?php echo h($picture); ?>" width="48" height="48" alt="" />
                            <?php endif; ?>
                            <!-- 一言表示 -->
                            <p><dt><span class="name"><a><?php echo h($name); ?></a></dt></span><?php echo h($message); ?></p>

                            <!-- 作成日の表示 -->
                            <div class="day"><a>作成日：<?php echo h($created); ?></a></div>

                            <!-- メッセージ削除 -->
                            <?php if ($_SESSION['id'] === $member_id): ?>
                                <div class="delete">
                                    <form method="POST" action="delete.php?id=<?php echo h($id); ?>" onsubmit="return confirm_test()">
                                        <input type="submit" value="削除"/>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?><p>その投稿は削除されたか、URLが間違えています</p>
<!---------------------------------------------------------------------------------------------------------------------------------->
                <?php endif; ?>
        </div>
    </div>
</body>

</html>