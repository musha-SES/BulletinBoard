<?php
session_start();

//session値のリセット
unset($_SESSION['id']);
unset($_SESSION['name']);
unset($_SESSION['photo']);

header('Location: login.php'); 
exit();
?>