<?php
/*---------------------------------------------------------*/
// 機能：新規会員登録用フォーム
// 名前、メール、パスワードは入力必須、写真は任意で登録できる
/*---------------------------------------------------------*/
session_start();
require('../library.php');

    if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
        $form = $_SESSION['form'];
    } else {
        $form = [ 
            'name' => '',
            'email' => '',
            'password' => '',
        ];
    }
    $error = [];

    //フォームの内容をチェック
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        /*-----名前チェック-----*/
        $form['name'] = filter_input(INPUT_POST,'name',FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($form['name'] === ''){
            $error['name']	= 'blank'; //errorメッセージ格納
        }

        /*-----メールチェック-----*/
        $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if ($form['email'] === '') {
            $error['email'] = 'blank';  
        } else { //同名メールがないかエラーチェック
            $db = dbConnect();
            $stmt = $db->prepare('select count(*) from members where email=?');
            if (!$stmt) {
                die($db->error);
            }
            $stmt->bind_param('s', $form['email']);
            $success = $stmt->execute();
            if (!$success){
                die($db->error);
            }

            $stmt->bind_result($cnt);
            $stmt->fetch();
            
            if ($cnt > 0) { //同名だと値が1入りエラー
                $error['email'] = 'duplicate';
            }
        }

        /*-----パスワードチェック-----*/
        $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($form['password'] === '') {
            $error['password'] = 'blank';
        } elseif (strlen($form['password']) < 4) {
            $error['password'] = 'length';
        }

        /*-----画像のチェック-----*/
        $image = $_FILES['image'];
        if ($image['name'] !== '' && $image['error'] === 0) {
            $type = mime_content_type($image['tmp_name']);
            if ($type !== 'image/png' && $type !== 'image/jpeg') {
                $error['image'] = 'type';
            }
        }

        if (empty($error)) {
            $_SESSION['form'] = $form;

            /*-----画像のアップロード-----*/
            if ($image['name'] !== ''){
                $filename = date('YmdHis') . '_' . $image['name']; //ファイルネームの設定
                if (!move_uploaded_file($image['tmp_name'], '../member_picture/' . $filename)) { //有効であればfilenameの名前でアプロード
                    die('ファイルのアップロードに失敗しました');
                }
                $_SESSION['form']['image'] = $filename;
            } else {
                $_SESSION['form']['image'] = '';
            }

            header('Location: check.php');
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>会員登録</title>

        <link rel="stylesheet" href="..\css\import.css"/>
    </head>

    <body>
        <div id="Wrap">
            <div id="head">
                <h1>会員登録</h1>
            </div>
<!--------------------------------------------------------------------------------------------------------------------------->
            <div id="content">
                <p>次のフォームに必要事項をご記入ください。</p><br>
                <form action="" method="post" enctype="multipart/form-data">
                        <dl>
                            <dt>ニックネーム<span class="required">必須</span></dt>
                                <dd>
                                    <input type="text" name="name" size="35" maxlength="50" value="<?php echo h($form['name']); ?>"/>
                                    <?php if (isset($error['name']) && $error['name'] === 'blank') : ?>
                                        <p class="error">* ニックネームを入力してください</p>
                                    <?php endif; ?>
                                </dd>

                            <dt>メールアドレス<span class="required">必須</span></dt>
                                <dd>
                                    <input type="text" name="email" size="35" maxlength="100" value="<?php echo h($form['email']); ?>"/>
                                        <?php if (isset($error['email']) && $error['email'] === 'blank') : ?>
                                            <p class="error">* メールアドレスを入力してください</p>
                                        <?php endif; ?>
                                        <?php if (isset($error['email']) && $error['email'] === 'duplicate') : ?>
                                            <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                                        <?php endif; ?>

                            <dt>パスワード<span class="required">必須</span></dt>
                                <dd>
                                    <input type="password" name="password" size="10" maxlength="20" value="<?php echo h($form['password']); ?>"/>
                                        <?php if (isset($error['password']) && $error['password'] === 'blank') : ?>
                                            <p class="error">* パスワードを入力してください</p>
                                        <?php endif; ?>
                                        <?php if (isset($error['password']) && $error['password'] === 'length') : ?>
                                            <p class="error">* パスワードは4文字以上で入力してください</p>
                                        <?php endif; ?>
                                </dd>

                            <dt>写真など</dt>
                                <dd>
                                    <input type="file" name="image" size="40" value=""/>
                                        <?php if (isset($error['image']) && $error['image'] === 'type') : ?>
                                            <p class="error">* 写真などは「.png」または「.jpg」の画像を指定してください</p>
                                        <?php endif; ?>
                                    <p class="error">画像を選択してください</p>
                                </dd>
                        </dl>
                    <div><input type="submit" value="入力内容を確認する"/></div>
                </form><br>
                <div class="Tag">
                    <span><a href="../">ログインフォームに戻る</a></span>
                </div>
            </div>
<!------------------------------------------------------------------------------------------------------------------------->
    </body>
</html>