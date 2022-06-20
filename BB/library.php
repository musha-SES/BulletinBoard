<?php
/*----------------------------------------*/
// 機能：呼び出し関数群
/*----------------------------------------*/

    //htmlspecialchars
    function h($value)
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }


    //DB接続
    function dbConnect()
    {
        $db = new mysqli('localhost:8891','root','root','mini_bbs');
        if (!$db) {
            die($db->error);
        }
        return $db;
    }

    //最終ログイン日時
    function lastJoin($value)
    {
        $db = dbConnect();
        $stmt = $db->prepare("update members set lastJoin = CURRENT_TIME() where members.id=$value");
        if (!$stmt) { 
            die($db->error);
        }
        $stmt->execute();
    }

    //アクセス日時
    function changeTheLog($value)
    {
        $db = dbConnect();
        $stmt = $db->prepare("update posts set access = CURRENT_TIME() where posts.hitokoto_id=$value");
        if (!$stmt) { 
            die($db->error);
        }
        $stmt->execute();
    }

    //最終編集日時
    function editTime($value)
    {
        $db = dbConnect();
        $stmt = $db->prepare("update profile set modified = CURRENT_TIME() where profile.id=$value");
        if (!$stmt) { 
            die($db->error);
        }
        $stmt->execute();
    }

    //トプ画の取得
    function loadMP(int $id)
    {
        $db = dbConnect();
        $stmt = $db->prepare('select name, picture from members where id=?');
            if (!$stmt) { 
                die($db->error);
            }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($name, $picture); 
        $stmt->fetch();
        return array($name, $picture);
    }

    //自己紹介呼び出し
    function loadProfile(int $id)
    {
        $db = dbConnect();
        $stmt = $db->prepare('select profile_text from profile where id=?');
        if (!$stmt) {
            die($db->NULL);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($profile);   
        $stmt->fetch();
        return $profile;
    }

    //profileの変更
    function changeTheProfile(string $profile,int $id)
    {
        $db = dbConnect();
        $stmt = $db->prepare('replace into profile (profile_text, id) values(?,?)');
        if (!$stmt) { 
            die($db->error);
        }

        $stmt->bind_param('si', $profile, $id);
        $success = $stmt->execute();
        if (!$success) { 
            die($db->error);
        }
    }

    //user名の変更
    function changeTheName(string $name,int $id)
    {
        $db = dbConnect();  
        $stmt = $db->prepare('update members set name=? where id=?'); 
        if (!$stmt) { 
            die($db->error);
        }

        $stmt->bind_param('si', $name, $id);
        $success = $stmt->execute();
        if (!$success) { 
            die($db->error);
        }
        return $name;
    }

    //画像名を変更
    function changeThePhoto(string $filename, int $id)
    {
        $db = dbConnect();  
        $stmt = $db->prepare('update members set picture=? where id=?'); 
        if (!$stmt) { 
            die($db->error);
        }

        $stmt->bind_param('si',  $filename, $id);
        $success = $stmt->execute();
        if (!$success) { 
            die($db->error);
        }
    }

    //mypage,userpageの最大ページ数を求める
    function maxPageCnt(int $id)
    {
        $db = dbConnect();  
        $counts = $db->prepare('select count(*) as cnt from posts where id=?');
        $counts->bind_param('i', $id);
        $counts->execute();
        $counts->bind_result($cnt); 
        $counts->fetch();
        return $cnt;
    }
