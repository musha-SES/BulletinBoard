<?php
session_start();

unset($_SESSION['id']);
unset($_SESSION['name']);
unset($_SESSION['photo']);

header('Location: login.php'); 
exit();
?>
s