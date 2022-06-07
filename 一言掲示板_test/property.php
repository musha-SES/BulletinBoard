<?php
session_start();
require('library.php');

    if (isset($_SESSION['id'])){ //値チェック
        $id =$_SESSION['id'];
        $nameName = $_SESSION['name'];
        $textProfile = loadProfile($id); 
    } else { 
        echo("Not Found error");
    }
$db = dbconnect(); //DB接続

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //ブラウザのリクエストがPOSTだった時
    //文字チェック + nameの変更処理
    $name = filter_input(INPUT_POST,'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $_SESSION['name'] = changeTheName($name,$id); 

    //文字チェック + profileTextの変更
    $profile = filter_input(INPUT_POST,'profile', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    changeTheProfile($profile,$id); //自己紹介文の設定

    //ファイルの変更処理 セットしてなければスルーされる
    if (empty($_FILES["photo"]["error"])){
        $photo = $_FILES['photo'];
        if($photo['name'] !== '' && $image['error'] === 0) {
            $type = mime_content_type($photo['tmp_name']);
            if($type !== 'photo/png' && $type !== 'photo/jpeg'){
                $error['photo'] = 'type';
            }
        }
        $filename = date('YmdHis') . '_' . $photo['name'];
        changeThePhoto($filename,$id);
        if(!move_uploaded_file($photo['tmp_name'], 'member_picture/' . $filename)){
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>設定</title>

    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <div id="wrap">
        <div id="head">
            <h1>Property</h1> 
        </div>
        <div id="content">
            <div class="titlebar">
                <p class="subject"><a href="index.php">Timeline</a></p>
                <p class="date"><a href="logout.php">Logout</a></p><br>
                <p class="mypage"><a href="mypage.php">MyPage</a></p>
            </div>

            <div>
                <form action="" method="post" enctype="multipart/form-data">
                    <dt><h5>名前の変更</h5></dt>
                        <dl><!-- 名前の変更 -->
                            <dd>
                            <input type="text" name="name" cols="20" rows="2" value="<?php echo h($nameName);?>"></input>
                            </dd>
                        </dl>

                    <dt><h5>プロフィール画像の変更</h5></dt>
                        <dl><!-- 画像の変更 -->
                            <dd>
                            <input type="file" name="photo" size="35" value=""/>
                                <?php if (isset($error['photo']) && $error['photo'] === 'type'): ?>
                                    <p class="error">* 「.png」または「.jpg」の画像を指定してください</p>
                                <?php endif; ?>
                                    <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                            </dd>
                        </dl>
                
                    <dt><h5>自己紹介の編集</h5></dt>
                        <dl><!-- 自己紹介文 -->
                            <dd>
                            <textarea type="text" name="profile" cols="50" rows="5" placeholder="テキストを入力"><?php echo h($textProfile); ?></textarea>
                            </dd>
                        </dl>
                    <input type="submit" value="done"/>
                </form>
            </div>
        </div>
    </div>
</body>
</html>