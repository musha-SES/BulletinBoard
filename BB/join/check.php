<?php
/*--------------------------------------------------------*/
// 機能：前ページで入力されたフォームの内容確認、DBに書き込む
/*--------------------------------------------------------*/
	session_start();
	require('../library.php');

	if (isset($_SESSION['form'])) { //formの空文字チェック
	$form = $_SESSION['form'];
	} else {
	header('Location: index.php');
	exit();
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$db = dbConnect();
		if ($form['image'] !== '') {
			var_dump($form);
			$stmt = $db->prepare('insert into members (name, email, password, picture) VALUES (?, ?, ?, ?)');
			if (!$stmt) {
				die($db->error);
			}
			$password = password_hash($form['password'], PASSWORD_DEFAULT);
			$stmt->bind_param('ssss', $form['name'], $form['email'], $password, $form['image']);
		} else {
			$photo = "sample.png";
			$stmt = $db->prepare('insert into members (name, email, password, picture) VALUES (?, ?, ?, ?)');
			if (!$stmt) {
				die($db->error);
			}
			$password = password_hash($form['password'], PASSWORD_DEFAULT);
			$stmt->bind_param('ssss', $form['name'], $form['email'], $password,$photo);
		}
		$success = $stmt->execute();
		if (!$success) {
			die($db->error);
		}

		unset($_SESSION['form']);
		header('Location: thanks.php');
	} 
?>
<!DOCTYPE html>
<html lang="ja">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" Content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" Content="ie=edge">
		<title>会員登録</title>

		<link rel="stylesheet" href="..\css\import.css" />
	</head>

	<body>
		<div id="Wrap">
			<div id="head">
				<h1>会員登録</h1>
			</div>

			<div id="Content">
				<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p><br>
				
				<form action="" method="post">
					
					<div class="Tag">
						<span>ニックネーム</span><br>
								<p>【<?php echo h($form['name']); ?>】</p>
	
						<span>メールアドレス</span><br>
							<p>【<?php echo h($form['email']); ?>】</p>
	
						<span>パスワード</span><br>
							<p>【表示されません】</p>
	
						<span>写真など</span><br>
							<img src="../member_picture/<?php echo h($form['image']); ?>" width="100" alt="" />
					</div>	
					
					<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>	
				</form>
			</div>
		</div>
	</body>
</html>