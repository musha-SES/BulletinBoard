<?php
/*---------------------------------------------------------*/
// 機能：タイムライン
// TL画面。postsテーブルに投稿したデータ投稿日時の降順に表示
// ヒトコトは写真、名前、message、投稿日時を表示する。
// 写真をクリック＞投稿ユーザページに飛ぶ 
// 日付をクリック＞ヒトコトの詳細画面に飛ぶ
/*---------------------------------------------------------*/
    session_start();
    require('library.php');

    if (isset($_SESSION['id']) && isset($_SESSION['name'])) { 
        $id = $_SESSION['id'];
        $name = $_SESSION['name'];
    } else { //入っていなければログインフォームに戻される
        header('Location: login.php');
        exit;
    }

    $db = dbConnect();

    // ページネーションの最大表示pageの計算
    $counts = $db->query('select count(*) as cnt from posts');
    $count = $counts->fetch_assoc();
    $max_page = floor(($count['cnt']+1)/5+1);
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
                            $stmt = $db->prepare('select p.hitokoto_id, p.id, p.message, p.created, m.name, m.picture from posts p, members m where m.id=p.id order by hitokoto_id desc limit ?,5'); //sqlのセット
                                if (!$stmt) {
                                    die($db->error);
                                }
                            $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
                            $page = ($page ?: 1);
                            $start = ($page - 1) * 5;
                            $stmt->bind_param('i', $start);
                            $success = $stmt->execute();
                                if (!$success) {
                                    die($db->error);
                                }
                            $stmt->bind_result($id, $member_id, $message, $created, $name, $picture);

                        while ($stmt->fetch()) : ?>
                            <!----- ヒトコトの表示 ----->
                            <div class="msg">
                                    <!-- トプ画表示 -->
                                    <?php if ($picture): ?> 
                                        <?php if ($_SESSION['id'] === $member_id) : //投稿者と自分のIDの不一致check ?>
                                            <div class="Icon"><a href="mypage.php?id=<?php echo h($_SESSION['id']); ?>"><img src ="member_picture/<?php echo h($picture); ?>"/></a></div>
                                            <?php else : ?>
                                            <div class="Icon"><a href="userPage.php?id=<?php echo h($member_id); ?>"><img src ="member_picture/<?php echo h($picture); ?>"/></a></div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- 投稿者と一言表示 -->
                                    <div class="Tag">
                                        <span><?php echo h($name); ?></span><br>
                                        <p><?php echo h($message); ?></p>
                                    </div>

                                    <!-- 作成日の表示 -->
                                    <div class="DayAndDelete">
                                        <div class="Day"><a href="view.php?id=<?php echo h($id); ?>"><p><?php echo h($created); ?></p></a></div>
                                        <!-- メッセージ削除機能 -->
                                        <div><?php if ($_SESSION['id'] === $member_id) : ?>
                                            <form method="POST" action="delete.php?id=<?php echo h($id);?>" onsubmit="return confirm_test()">
                                                <input type="image" src="images/cash.png"/>
                                            </form>
                                        <?php endif; ?></div>
                                    </div>
                            </div>
<!--------------------------------------------------------------------------------------------------------------------------->
                        <?php endwhile; ?>
                        <!-- ページネーション -->
                        <div class="PageNation">
                                <?php if ($page > 1) : ?>
                                    <a href="?page=<?php echo $page-1; ?>"><span1><?php echo $page-1; ?>ページ目へ</span1></a>
                                <?php endif; ?>
                                <!-- 投稿数が5の倍数を超えたら次ページボタン表示。投稿数が5の倍数以下なら非表示(５の倍数以下でも表示されるため) -->
                                <?php if ($count['cnt'] > $page*5 && $page < $max_page ) :?>
                                    <a href="?page=<?php echo $page+1; ?>"><span2><?php echo $page+1; ?>ページ目へ</span2></a>
                                <?php endif; ?>
                        </div>
                    </div>
                    <!-- フッター表示 -->
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