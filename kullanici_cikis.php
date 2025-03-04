<?php
session_start();
session_destroy();
header("Location: index.php"); // Ana sayfaya yönlendir
exit();
?>
