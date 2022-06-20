<?php
/*--------------------------------------------------------------------*/
// 機能：postsテーブルの指定したmessage_idのデータを削除、画面からも削除
/*--------------------------------------------------------------------*/
    session_start();
    require('library.php');

    if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
        $id = $_SESSION['id'];
        $name = $_SESSION['name'];
    } else {
        header('Location: login.php');
        exit;
    }

    //urlパラメーターIDの数値チェック
    $post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$post_id) {
        header('Location: index.php');
        exit();
    }

    //DBデータ削除処理
    $db = dbConnect();
    $stmt = $db->prepare('delete from posts where hitokoto_id=? and id=? limit 1');
    if (!$stmt) {
        die($db->error);
    }

    $stmt->bind_param('ii', $post_id, $id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    
    //アクセス元にリダイレクト
    header('Location:' . $_SERVER['HTTP_REFERER']);
    exit();
?>
