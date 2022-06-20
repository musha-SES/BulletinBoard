<?php
/*------------------------------------------------------------------------*/
// 機能：登録情報の変更、退会手続き
// ログインユーザーは自分の登録した名前・トプ画の変更、自己紹介文の設定ができる
// 退会用フォームにジャンプするボタンがある(このページではまだ退会できない)
/*------------------------------------------------------------------------*/
    session_start();
    require('library.php');

        if (isset($_SESSION['id'])) {
            $id =$_SESSION['id'];
            $nameName = $_SESSION['name'];
            $textProfile = loadProfile($id); 
        } else { 
            echo("Not Found error");
        }

    $db = dbConnect();

    $error = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
        $name = filter_input(INPUT_POST,'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
        $_SESSION['name'] = changeTheName($name,$id); // nameの変更処理

        $profile = filter_input(INPUT_POST,'profile', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
        changeTheProfile($profile,$id); // profileTextの変更

        if (empty($_FILES["photo"]["error"])) {
            $photo = $_FILES['photo'];
            if ($photo['name'] !== '' && $image['error'] === 0) {
                $type = mime_content_type($photo['tmp_name']);
                if ($type !== 'photo/png' && $type !== 'photo/jpeg') {
                    $error['photo'] = 'type';
                }
            }

            $filename = date('YmdHis') . '_' . $photo['name']; 
            changeThePhoto($filename,$id); //photoの変更

            if (!move_uploaded_file($photo['tmp_name'], 'member_picture/' . $filename)) {
                die('ファイルのアプロードに失敗しました');
            }
            $_SESSION['photo'] = $filename; 
        } 
        header('Location: mypage.php');
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" Content="IE=edge">
        <meta name="viewport" Content="width=device-width, initial-scale=1.0">
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
                    <div class=Propertys>
                        <form action="" method="post" enctype="multipart/form-data">
                            <h5>名前の変更</h5>
                            <!-- 名前の変更 -->
                                <dl>
                                    <dd><input type="text" name="name" cols="20" rows="2" maxlength="50" value="<?php echo h($nameName);?>"></input></dd>
                                </dl>

                            <h5>プロフィール画像の変更</h5>
                            <!-- 画像の変更 -->
                                <dl>
                                    <dd>
                                        <input type="file" name="photo" size="35" value=""/>
                                            <?php if (isset($error['photo']) && $error['photo'] === 'type') : ?>
                                                <p class="error">* 「.png」または「.jpg」の画像を指定してください</p>
                                            <?php endif; ?>
                                    </dd>
                                </dl>
                        
                            <h5>自己紹介の編集</h5>
                            <!-- 自己紹介文 -->
                                <dl>
                                    <dd>
                                        <div>
                                            <textarea type="text" name="profile" cols="50" rows="5" maxlength="100" placeholder="テキストを入力"><?php echo $textProfile; ?></textarea>
                                        </div>
                                            <input type="submit" class="button" value="done"/>
                                    </dd>
                                </dl>
                        </form>

                        <!-- 退会 -->
                        <h5>ヒトコトを退会する</h5>
                        <dl>
                            <dd>
                                <a href="withdrawal.php">退会はこちら</a>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <!-- フッター表示 -->
                <footer>
                    <div class="Blockarea">
                        <a href="index.php">
                            <div class="Footertags"><p>Timeline</p></div>
                        </a>
                        <p>|</p>
                        <a href="hitokoto.php">
                            <div class="Footertags"><img src="images/kakiko.png" style="width: 40px;"></div>
                        </a>
                        <p>|</p>
                        <a href="mypage.php">
                            <div class="Footertags"><p>MyPage</p></div>
                        </a>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>