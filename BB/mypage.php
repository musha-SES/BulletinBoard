<?php
/*--------------------------------------------------------------------------*/
// 機能：ログインユーザーのマイページ
// 設定した名前と画像の表示、その下に自分が今まで投稿したヒトコトが表示される
// ここからのみログアウトと登録情報の変更・退会ができる設定ページにジャンプできる
/*--------------------------------------------------------------------------*/
    session_start();
    require('library.php');

    if (isset($_SESSION['id'])) {
        $id =$_SESSION['id'];
        $name = $_SESSION['name'];
        $photo = $_SESSION['photo'];
        $profile = loadProfile($id);
    } else { 
        echo("Not Found error");
    }

    $db = dbConnect();

    // ページネーションの最大表示pageの計算
    $count = maxPageCnt($id);
    $max_page =  floor(($count+1)/5+1);
?>

<script>
    // 削除するときに確認用アラートを表示
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

        <link rel="stylesheet" href="css\import.css"/>
    </head>
    <body>
        <div id="Wrap">
            <div id="head">
                <a href="index.php"><h1>ヒトコト --Mypage--</h1></a>
                <div class="header">
                    <a href="logout.php"><p>Logout</p></a>
                    <a href="property.php"><p>Property</p></a>                
                </div>
            </div>
            <div class="wrapper">
                <div id="content">
                    <!-- 名前とプロフィール画像の表示 -->
                    <div class="profile">
                        <h3><?php echo h($name); ?></h3>
                            <?php if ($photo) : ?>
                                <div class="Icon-circle">
                                    <img src="member_picture/<?php echo h($photo); ?>"/>
                                </div>
                            <?php endif; ?><br>
                        <p><?php echo loadProfile($id); ?></p>
                    </div>
                        
                    <!----- 自分の投稿した一言を一覧表示 ----->
                    <h4>--- hitokoto ---</h4>
                    <?php
                        $sth = $db->prepare('select hitokoto_id, message, id, created  from posts where id=? order by hitokoto_id desc limit ? , 5');
                            if (!$sth) { 
                                die($db->error);
                            }
                            // 現在表示されてるページ数を格納する変数を用意
                            $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
                            $page = ($page ?: 1);
                            $start = ($page - 1) * 5; //最初の表示IDの指定
                            $sth->bind_param('ii',$id,$start);
                            $sth->execute();
                            $sth->bind_result($hid, $message, $id, $created);
                    ?>

                    <?php while($sth->fetch()): //投稿日時の降順で表示 ?>
                        <div class="msg">
                            <div class="Icon" style="pointer-events:none;"><img src ="member_picture/<?php echo h($photo); ?>"/></div>
                            <!-- 一言表示 -->
                            <div class="tag">
                                <span><?php echo h($name); ?></span><br>
                                <p><?php echo h($message); ?></p>
                            </div>
                                
                            <!-- 作成日の表示 -->
                            <div class="DayAndDelete">
                                <div class="Day"><a href="view.php?id=<?php echo h($hid); ?>"><p><?php echo h($created); ?></p></a></div>

                            <!-- メッセージ削除機能 -->
                                <div><?php if ($_SESSION['id'] === $id) : ?> 
                                    <form method="POST" action="delete.php?id=<?php echo h($hid); ?>" onsubmit="return confirm_test()">
                                        <input type="image" src="images/cash.png"/>
                                    </form>
                                <?php endif; ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
     
                    <!-- ページネーション -->
                    <div class="PageNation">
                            <?php if ($page>1) : ?>
                                <a href="?page=<?php echo $page-1; ?>"><span1><?php echo $page-1; ?>ページ目へ</span1></a>
                            <?php endif; ?>
                            <?php if ($count > $page*5 && $page < $max_page) : ?>
                                <a href="?page=<?php echo $page+1; ?>"><span2><?php echo $page+1; ?>ページ目へ</span2></a>
                            <?php endif; ?>
                    </div>
                </div>

                <!-- フッターの表示 -->
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
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>