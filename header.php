<?php
session_start();

// Veritabanı Bağlantısı
$servername = "localhost";
$username = "volaresoft-accounting";
$password = "U4^Nj8r+y@C.i5X*";
$database = "accounting";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// Kullanıcı Doğrulama
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ön Muhasebe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #212529;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 15px 20px;
            display: block;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #343a40;
            border-left: 3px solid #007bff;
        }
        .sidebar .logo {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding-bottom: 20px;
        }
        .submenu {
            display: none;
            flex-direction: column;
            padding-left: 20px;
        }
        .submenu a {
            font-size: 0.9rem;
        }
        .sidebar a[data-toggle="submenu"]:hover + .submenu,
        .submenu:hover {
            display: flex;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Ön Muhasebe</div>
        <a href="/accounting/index.php" class="active">Dashboard</a>
        <a href="/accounting/satis.php">Satış</a>
        <a href="/accounting/faturalar.php">Faturalarım</a>
        <a href="/accounting/calisanlar.php">Çalışanlar</a>
        <a href="/accounting/calisan_odemeleri.php">Çalışan Ödemeleri</a>
        <a href="/accounting/alislar_fatura.php">Alışlar Fatura</a>
        <a href="/accounting/gelirler.php">Gelirlerim</a>
        <a href="/accounting/giderler.php">Giderlerim</a>
        <a href="/accounting/ayarlar.php">Ayarlar</a>
        <a href="#" data-toggle="submenu">Hesaplama Araçları</a>
        <div class="submenu">
            <a href="/accounting/hesaplama_araclari/kdv_hesaplama.php">KDV Hesaplama</a>
            <a href="/accounting/hesaplama_araclari/taksit_hesaplama.php">Taksit Hesaplama</a>
            <a href="/accounting/hesaplama_araclari/kar_zarar_analizi.php">Kar/Zarar Analizi</a>
            <a href="/accounting/hesaplama_araclari/doviz_cevirici.php">Döviz Çevirici</a>
            <a href="/accounting/hesaplama_araclari/canli_doviz.php">Canlı Döviz Kurları</a>
        </div>
        <a href="/accounting/logout.php" class="text-danger">Çıkış Yap</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
