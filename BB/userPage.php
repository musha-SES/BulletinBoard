<?php
/*---------------------------*/
// 機能:他ユーザーのマイページ
// 閲覧のみ可能
/*---------------------------*/
    session_start();
    require('library.php');

    // urlからidを取得
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $id = intval($id);
        $user = loadMP($id);
        unset($_SESSION['userID']);
        $_SESSION['userID'] = $id;
    } else {
        if(isset($_SESSION['userID'])) {
            $id = $_SESSION['userID'];
            $user = loadMP($id);
        } else {
            echo("Not Found error");
        }
    }

    $db = dbConnect();

    // ページネーションの最大表示pageの計算
    $count = maxPageCnt($id);
    $max_page =  floor(($count+1)/5+1);
?>

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
                <a href="index.php"><h1>ヒトコト --Userpage--</h1></a> 
            </div>
                <div class="wrapper">
                    <div id="content">
                        <!-- 名前とプロフィール画像の表示 -->
                        <div class="profile">
                            <h3><?php echo h($user[0]); ?></h3>
                                <?php if ($user[1]) : ?>
                                    <div class="Icon-circle">
                                        <img src="member_picture/<?php echo h($user[1]); ?>"/>
                                    </div>
                                <?php endif; ?><br>
                            <a><?php echo loadProfile($id); ?></a>
                        </div>    

                        <!----- ユーザーの投稿した一言を一覧表示 ----->
                        <h4>--- hitokoto ---</h4>
                        <?php
                            $sth = $db->prepare('SELECT p.hitokoto_id, p.message, m.name, p.created 
                            FROM members m LEFT OUTER JOIN posts p ON p.id = m.id 
                            WHERE m.id = ? order by p.hitokoto_id desc limit ? , 5'); //sqlのセット
                                if (!$sth) { 
                                    die($db->error);
                                }
                            // 現在表示されてるページ数を格納する変数を用意
                            $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
                            $page = ($page ?: 1);
                            $start = ($page - 1) * 5; //最初の表示IDの指定
                            $sth->bind_param('ii',$id,$start);
                            $sth->execute();
                            $sth->bind_result($hid, $message, $name, $created);
                        ?>

                        <?php while($sth->fetch()): ?>
                            <div class="msg">
                            <div class="Icon" style="pointer-events:none;"><img src ="member_picture/<?php echo h($user[1]); ?>"/></div>
                                <!-- 一言表示 -->
                                <div class="Tag">
                                    <span><?php echo h($name); ?></span><br>
                                    <p><?php echo h($message); ?></p>
                                </div>

                                <!-- 作成日の表示 -->
                                <div class="DayAndDelete" style="height:18.45px;">
                                    <div class="Day"><a href="view.php?id=<?php echo h($hid); ?>"><span><?php echo h($created); ?></span></a></div> 
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