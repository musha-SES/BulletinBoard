<?php
/*----------------------------------------*/
// 機能：呼び出し関数群
/*----------------------------------------*/
?>
<?php //htmlspecialchars
    function h($value){
        return htmlspecialchars($value, ENT_QUOTES);
    }
?>

<?php //DB接続
    function dbconnect(){
        $db = new mysqli('localhost:8891','root','root','mini_bbs');
        if(!$db){ //エラー処理
            die($db->error);
        }
        return $db;
    }
?>

<?php //最終ログイン日時
    function lastJoin($value){
        $db = dbconnect();
        $stmt = $db->prepare("update members set lastJoin = CURRENT_TIME() where members.id=$value");
        if(!$stmt) { //エラー処理
            die($db->error);
        }
        $stmt->execute();
    }
?>

<?php //最終アクセス日時
    function changeTheLog($value){
        $db = dbconnect();
        $stmt = $db->prepare("update posts set access = CURRENT_TIME() where posts.hitokoto_id=$value");
        if(!$stmt){ //エラー処理
            die($db->error);
        }
        $stmt->execute();
    }
?>

<?php //最終編集日時
    function editTime($value){
        $db = dbconnect();
        $stmt = $db->prepare("update profile set modified = CURRENT_TIME() where profile.id=$value");
        if(!$stmt) { //エラー処理
            die($db->error);
        }
        $stmt->execute();
    }
?>

<?php //トプ画の取得
    function loadMP(int $id){
        $db = dbconnect();
        $stmt = $db->prepare('select name, picture from members where id=?');
            if (!$stmt){ //エラー処理
                die($db->error);
            }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($name, $picture); 
        $stmt->fetch();
        return array($name, $picture);
    }
?>

<?php //自己紹介呼び出し
    function loadProfile(int $id){
        $db = dbconnect();
        $stmt = $db->prepare('select profile_text from profile where id=?');
        if (!$stmt){//エラー処理
            die($db->NULL);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($profile);   
        $stmt->fetch();
        return $profile;
    }
?>

<?php //profileの変更
    function changeTheProfile(string $profile,int $id){
        $db = dbconnect();
        $stmt = $db->prepare('replace into profile (profile_text, id) values(?,?)'); //
        if(!$stmt) { //エラー処理
            die($db->error);
        }

        $stmt->bind_param('si', $profile, $id);
        $success = $stmt->execute();
        if(!$success){ //エラー処理
            die($db->error);
        }
    }
?>

<?php //user名の変更
    function changeTheName(string $name,int $id){
        $db = dbconnect();  
        $stmt = $db->prepare('update members set name=? where id=?'); 
        if(!$stmt){ //エラー処理
            die($db->error);
        }

        $stmt->bind_param('si', $name, $id);
        $success = $stmt->execute();
        if(!$success){ //エラー処理
            die($db->error);
        }
        return $name;
    }
?>

<?php //画像名を変更
    function changeThePhoto(string $filename, int $id){
        $db = dbconnect();  
        $stmt = $db->prepare('update members set picture=? where id=?'); 
        if(!$stmt){ //エラー処理
            die($db->error);
        }

        $stmt->bind_param('si',  $filename, $id);
        $success = $stmt->execute();
        if(!$success){ //エラー処理
            die($db->error);
        }
    }
?>

<?php //mypage,userpageの最大ページ数を求める
    function max_pageMU(int $id){
        $db = dbconnect();  
        $counts = $db->prepare('select count(*) as cnt from posts where id=?');
        $counts->bind_param('i', $id);
        $counts->execute();
        $counts->bind_result($cnt); 
        $counts->fetch();
        return $cnt;
    }
?>