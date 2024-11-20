<?php
session_start(); // Oturum başlat
session_destroy(); // Tüm oturum verilerini sil
header("Location: login.php"); // Login sayfasına yönlendir
exit(); // Güvenlik için scripti sonlandır
?>
