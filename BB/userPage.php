<?php
require('library.php');

//urlからidを取得
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $id = intval($id);
}

$db = dbconnect(); //DB接続
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ヒトコト</title>

        <link rel="stylesheet" href="style.css"/>
    </head>
<body>
<!--------------------------------------------- 名前とプロフィール画像の表示 ------------------------------------------------------------>
        <div id="wrap">
            <div id="head">
                <a href="index.php"><h1>ヒトコト --userPage--</h1></a> 
            </div>
                <div class="wrapper">
                    <div id="content">
                            <!-- 名前とプロフィール画像の表示 -->
                            <?php $result = loadMP($id); ?>
                                <div class="profile">
                                    <h3>[<?php echo h($result[0]); ?>]</h3>
                                        <?php if ($result[1]): ?>
                                            <div class="icon-circle">
                                                <img src="member_picture/<?php echo h($result[1]); ?>" width="150" />
                                            </div>
                                        <?php endif; ?><br>
                                    <a><?php echo loadProfile($id); ?></a>
                                </div>     
<!------------------------------------------------------------------------------------------------------------------------------------->
                        
<!--------------------------------------------- ユーザーの投稿した一言を一覧表示 -------------------------------------------------------->
                            <h4>--- hitototo ---</h4>
                            <?php //一言データの取得sql
                                $sth = $db->prepare('SELECT p.hitokoto_id, p.message, m.name, p.created 
                                FROM members m LEFT OUTER JOIN posts p ON p.id = m.id 
                                WHERE m.id = ? order by p.hitokoto_id desc'); //sqlのセット
                                    if (!$sth) { //エラー処理
                                        die($db->error);
                                    }
                                $sth->bind_param('i',$id);
                                $sth->execute();
                                $sth->bind_result($hid, $message, $name, $created); //各変数に値を挿入
                            ?>

                            <?php while($sth->fetch()): //値がなくなるまで下の処理を実行 ?>
                                <div class="msg">
                                    <!-- 一言表示 -->
                                    <div class="tag">
                                        <span><?php echo h($name); ?></span><br>
                                        <p><?php echo h($message); ?></p>
                                    </div>
                                    <!-- 作成日の表示 -->
                                    <div class="dayAndDelete">
                                        <div class="day"><a href="view.php?id=<?php echo h($hid); ?>"><p><?php echo h($created); ?></p></a></div> 
                                    </div>
                                </div>
                            <?php endwhile; ?>
<!------------------------------------------------------------------------------------------------------------------------------------->
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