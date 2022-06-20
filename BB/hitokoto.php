<?php
/*------------------------------------------------------------*/
// 機能：メッセージの投稿画面 postsテーブルに記入したtextを追加
/*------------------------------------------------------------*/
    session_start();
    require('library.php');

    if (isset($_SESSION['id']) && isset($_SESSION['name'])) { 
        $id = $_SESSION['id'];
        $name = $_SESSION['name'];
    } else {
        header('Location: login.php');
        exit;
    }

    $db = dbConnect();

    //メッセージの投稿
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = filter_input(INPUT_POST,'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $stmt = $db->prepare('insert into posts (message, id) values(?,?)'); //
        if (!$stmt) {
            die($db->error);
        }

        $stmt->bind_param('si', $message, $id);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }

        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="ja">
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
                <a href="index.php"><h1>ヒトコト</h1></a>
            </div>
                <div class="wrapper">
                    <div id="content" style="max-height: 900px;">
                            <form action="" method="post">
                                <div class="linkFont">
                                    <dl>
                                        <!-- マイページ -->
                                        <dt><a href="mypage.php?id=<?php echo h($id); ?>"><?php echo h($name); ?></a> please enter a message...</dt>
                                        <!-- テキストエリア -->
                                        <dd><textarea name="message" cols="50" rows="5" placeholder="ヒトコト" maxlength="100" autofocus required></textarea></dd>
                                    </dl>
                                </div>
                                <div>
                                    <p style="padding-bottom: 15px;"><input type="submit" class="button" value="POST"/></p>
                                </div>
                            </form>
                    </div>
                    <!-- フッター表示 -->
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