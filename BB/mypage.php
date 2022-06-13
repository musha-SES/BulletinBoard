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
<script>
//確認ポップアップ表示
function confirm_test() {
    var select = confirm("このヒトコトを本当に削除してよろしいですか？");
    return select;
}
</script>

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
                <h1>ヒトコト --MyPage--</h1> 
                <p class="date"><a href="logout.php">Logout </a></p><br>
                <p class="property"><a href="property.php">Property </a></p>
            </div>
            <div class="wrapper">
                <div id="content">
                        <!-- 名前とプロフィール画像の表示 -->
                        <div class="profile">
                            <h3><?php echo h($name); ?></h3>
                                <?php if ($photo): ?>
                                    <div class="icon-circle">
                                        <img src="member_picture/<?php echo h($photo); ?>"/>
                                    </div>
                                <?php endif; ?><br>
                            <a><?php echo loadProfile($id); ?></a>
                        </div>     
<!------------------------------------------------------------------------------------------------------------------------------------->
                    
<!--------------------------------------------- 自分の投稿した一言を一覧表示 ------------------------------------------------------------>
                    <h4>--- hitototo ---</h4>
                    <?php //一言データの取得sql
                         $sth = $db->prepare('select hitokoto_id, message, id, created  from posts where id=? order by hitokoto_id desc'); //sqlのセット
                             if (!$sth) { //エラー処理
                                 die($db->error);
                             }
                         $sth->bind_param('i',$id);
                         $sth->execute();
                         $sth->bind_result($hid, $message, $id, $created); //各変数に値を挿入
                    ?>

                    <?php while($sth->fetch()): //値がなくなるまで下の処理を実行 ?>
                        <div class="msg">
                            <!-- 一言表示 -->
                            <div class="tag">
                                <span><?php echo h($name); ?></span><br>
                                <?php echo h($message); ?>
                            </div>
                            
                            <!-- 作成日の表示 -->
                            <div class="dayAndDelete">
                                <div><a href="view.php?id=<?php echo h($hid); ?>"><?php echo h($created); ?></a></div>
                            <!-- メッセージ削除機能 -->
                                <div><?php if ($_SESSION['id'] === $id): ?>
                                    <form method="POST" action="delete.php?id=<?php echo h($hid); ?>" onsubmit="return confirm_test()">
                                        <input type="image" src="images/cash.png"/>
                                    </form>
                                <?php endif; ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
<!------------------------------------------------------------------------------------------------------------------------------------->
                </div>
            <footer>
                <div class="blockArea">
                    <a href="index.php" style="text-decoration: none;">
                        <div class="footer_tags"><p>Timeline</p></div>
                    </a>
                    <a href="hitokoto.php" style="text-decoration: none;">
                        <div class="footer_tags"><p>HiToKoTo</p></div>
                    </a>
                    <a href="mypage.php" style="text-decoration: none;">
                        <div class="footer_tags"><p>MyPage</p></div>
                    </a>
                    <div class="clear"></div>
                </div>
            </footer>
            </div>
        </div>
</body>
</html>