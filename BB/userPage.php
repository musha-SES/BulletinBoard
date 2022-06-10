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
                <h1>ヒトコト --userPage--</h1> 
            </div>
                <div id="content">
                    <div class="titlebar">
                        <p class="subject">&laquo<a href="index.php">Timeline</a></p>
                    </div>
                        <!-- 名前とプロフィール画像の表示 -->
                        <?php $result = loadMP($id); ?>
                            <div class="profile">
                                <h3>[<?php echo h($result[0]); ?>]</h3>
                                    <?php if ($result[1]): ?>
                                        <img src="member_picture/<?php echo h($result[1]); ?>" width="150" />
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
                                <p><dt><span class="name"><a><?php echo h($name); ?></a></dt></span><?php echo h($message); ?></p>
                                
                                <!-- 作成日の表示 -->
                                <div class="day">
                                    <a href="view.php?id=<?php echo h($hid); ?>">作成日：<?php echo h($created); ?></a>
                                </div>
                                
                            </div>
                        <?php endwhile; ?>
<!------------------------------------------------------------------------------------------------------------------------------------->
                </div>
        </div>
</body>
</html>