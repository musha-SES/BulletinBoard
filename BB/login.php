<?php
/*-----------------------------------------------------------------------*/
// 機能:ログインフォーム
// ログインが成功すると、ログインユーザーのID、名前、トプ画アドレスを生成する
/*-----------------------------------------------------------------------*/

    session_start();
    require('library.php');

    //空文字代入
    $error = [];
    $email = '';
    $password = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email =filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($email === '' || $password === '') {
            $error['login'] = 'blank';
        } else {
            $db = dbConnect();
            $stmt = $db->prepare('select id, name, password from members where email=? limit 1');
            if (!$stmt) { 
                die($db->error);
            }
            $stmt->bind_param('s', $email);
            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }
            $stmt->bind_result($id, $name, $hash);
            $stmt->fetch();

            if (password_verify($password, $hash)) { // パスワードがハッシュにマッチするかどうかを調べる
                session_regenerate_id(); //session_idの再生成 ※生成
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $result = loadMP($id); // 画像アドレスの取得
                $_SESSION['photo'] = $result[1]; 
                lastJoin($id); // ログイン日時の更新

                header('Location: index.php'); 
                exit();
            } else {
                $error['login'] = 'failed';
            }
        }
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="css\import.css"/>
        <title>ログインする</title>
    </head>

    <body>
        <div id="Wrap">
            <div id="head">
                <h1>ログインする</h1>
            </div>
            <div id="content">
                <div id="lead">
                    <p>メールアドレスとパスワードを記入してログインしてください。</p>
                    <p>入会手続きがまだの方はこちらからどうぞ。</p>
                    <p>&raquo;<a href="join/">入会手続きをする</a></p>
                </div>
                <form action="" method="post">
                    <dl>
                        <dt>メールアドレス</dt>
                        <dd>
                            <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($email); ?>"/>
                            <!-- 空白エラー -->
                            <?php if (isset($error['login']) && $error['login'] === 'blank') : ?>
                                <p class="error">* メールアドレスとパスワードをご記入ください</p>
                            <?php endif; ?>
                            <!-- 入力条件不足エラー -->
                            <?php if (isset($error['login']) && $error['login'] === 'failed') : ?>
                                <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
                            <?php endif; ?>
                        </dd>
                        
                        <dt>パスワード</dt>
                        <dd><input type="password" name="password" size="35" maxlength="255" value="<?php echo h($password); ?>"/></dd>
                    </dl>
                    <div>
                        <input type="submit" class="button" value="ログインする"/>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
