<?php
session_start();
unset($_SESSION['client_id']);
unset($_SESSION['client_name']);
session_destroy();
header("Location: client_login.php");
exit();
?>