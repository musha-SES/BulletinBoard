<?php
session_start();
require('library.php');

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

$db = dbconnect();
changeTheLog($id); // 最終アクセスの更新
?>

<script>
function confirm_test() { // 問い合わせるボタンをクリックした場合
    document.getElementById('popup').style.display = 'block';
    return false;
}
 
function okfunc() { // OKをクリックした場合
    document.contactform.submit();
}
 
function nofunc() { // キャンセルをクリックした場合
    document.getElementById('popup').style.display = 'none';
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
        <?php 
        $stmt = $db->prepare('select p.id, p.member_id, p.message, p.created, m.name, m.picture from posts p, members m where p .id=? and m.id=p.member_id order by id desc');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
        $stmt->bind_result($id, $member_id, $message, $created, $name, $picture);
        if ($stmt->fetch()):
        ?>
        <div class="msg">
            <!-- トプ画表示 -->
            <?php if ($picture): ?> 
                <img src ="member_picture/<?php echo h($picture); ?>" width="48" height="48" alt="" />
            <?php endif; ?>
                <!-- 一言表示 -->
                <p><dt><span class="name"><a><?php echo h($name); ?></a></dt></span>                
                <?php echo h($message); ?>
                </p>

            <div class="day">
            <!-- 作成日の表示 -->
                    <a>作成日：<?php echo h($created); ?></a>
            </div>

            <!-- メッセージ削除機能 -->
                <div class="delete">
                <?php if ($_SESSION['id'] === $member_id): ?>
                        <form name="contactform" action="delete.php?id=<?php echo h($id); ?>">
                            <input type="submit" value="削除" name="contact" onclick="return confirm_test()"/>
                        </form>
                </div>
                <div id="popup"	style="display: none;">
                        削除しますか？<br />
                        <button id="ok" onclick="okfunc()" style="margin: top 20px;">削除</button>
                        <button id="no" onclick="nofunc()">キャンセル</button>
                </div>
                <?php endif; ?>
        </div>
        <?php else: ?>
        <p>その投稿は削除されたか、URLが間違えています</p>
        <?php endif; ?>
    </div>
</div>
</body>

</html>