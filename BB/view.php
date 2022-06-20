<?php
/*----------------------------------------*/
// 機能：クリックしたヒトコトを一画面表示
/*----------------------------------------*/
    session_start();
    require('library.php');

    if (isset($_SESSION['id']) && isset($_SESSION['name'])) { 
        $id = $_SESSION['id'];
    } else {
        header('Location: login.php');
        exit;
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$id) {
        header('Location: index.php');
        exit();
    }

    $db = dbConnect();
    changeTheLog($id); // 最終アクセスの更新
?>
<script>
     // 削除するときに確認用アラートを表示
    function confirm_test() {
        var select = confirm("このヒトコトを本当に削除してよろしいですか？");
        return select; 
    }
</script>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" Content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" Content="ie=edge">
        <title>ヒトコト</title>

        <link rel="stylesheet" href="css\import.css"/>
    </head>

    <body>
        <div id="Wrap">
            <div id="head">
                <a href="index.php"><h1>ヒトコト</h1></a>
            </div>
            <div class="Wrapper">
                <div id="Content">
                    <?php
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

                        if ($stmt->fetch()) : ?>
                            <div class="msg">
                                <!-- トプ画表示 -->
                                <?php if ($picture) : ?>
                                    <?php if ($_SESSION['id'] === $member_id) : //投稿者と自分のIDの不一致check ?>
                                        <div class="Icon"><a href="mypage.php?id=<?php echo h($_SESSION['id']); ?>"><img src ="member_picture/<?php echo h($picture); ?>"/></a></div>
                                        <?php else : ?>
                                        <div class="Icon"><a href="userPage.php?id=<?php echo h($member_id); ?>"><img src ="member_picture/<?php echo h($picture); ?>"/></a></div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- 一言表示 -->
                                <div class="Tag">
                                    <span><?php echo h($name); ?></span><br>
                                    <p><?php echo h($message); ?></p>
                                </div>

                                <!-- 作成日の表示 -->
                                <div class="DayAndDelete">
                                    <div><a>作成日：<?php echo h($created); ?></a></div>
                                    <!-- メッセージ削除 -->
                                    <div><?php if ($_SESSION['id'] === $member_id) : ?>
                                        <form method="POST" action="delete.php?id=<?php echo h($id); ?>" onsubmit="return confirm_test()">
                                            <input type="image" src="images/cash.png"/>
                                        </form>
                                    <?php endif; ?></div>
                                </div>
                            </div>
                        <?php else : ?><p>そのヒトコトは削除されました</p>
                    <?php endif; ?>
                </div>

                <!-- フッターの表示 -->
                <footer>
                    <div class="Blockarea">
                        <a href="index.php">
                            <div class="Footertags"><p>Timeline</p></div>
                        </a>
                        <p1>|</p1>
                        <a href="hitokoto.php">
                            <div class="Footertags"><img src="images/kakiko.png" style="width: 40px;"></div>
                        </a>
                        <p1>|</p1>
                        <a href="mypage.php">
                            <div class="Footertags"><p>MyPage</p></div>
                        </a>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>