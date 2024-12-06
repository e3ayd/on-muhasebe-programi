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

// Aktif sayfayı belirlemek için PHP_SELF kullanıyoruz
$current_page = basename($_SERVER['PHP_SELF']);
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
        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            width: auto;
        }
        .logo img {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo"><img src="logo-light.png" alt="Logo"></div>
        <a href="/accounting/index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="/accounting/satis.php" class="<?= $current_page == 'satis.php' ? 'active' : '' ?>">Satış</a>
        <a href="/accounting/musteri.php" class="<?= $current_page == 'musteri.php' ? 'active' : '' ?>">Müşteriler</a>
        <a href="/accounting/faturalar.php" class="<?= $current_page == 'faturalar.php' ? 'active' : '' ?>">Faturalarım</a>
        <a href="/accounting/calisanlar.php" class="<?= $current_page == 'calisanlar.php' ? 'active' : '' ?>">Çalışanlar</a>
        <a href="/accounting/calisan_odemeleri.php" class="<?= $current_page == 'calisan_odemeleri.php' ? 'active' : '' ?>">Çalışan Ödemeleri</a>
        <a href="/accounting/alislar_fatura.php" class="<?= $current_page == 'alislar_fatura.php' ? 'active' : '' ?>">Alışlar Fatura</a>
        <a href="/accounting/gelirler.php" class="<?= $current_page == 'gelirler.php' ? 'active' : '' ?>">Gelirlerim</a>
        <a href="/accounting/giderler.php" class="<?= $current_page == 'giderler.php' ? 'active' : '' ?>">Giderlerim</a>
        <a href="/accounting/notlarim.php" class="<?= $current_page == 'notlarim.php' ? 'active' : '' ?>">Notlarım</a>
        <a href="/accounting/ayarlar.php" class="<?= $current_page == 'ayarlar.php' ? 'active' : '' ?>">Ayarlar</a>
        <a href="#" data-toggle="submenu">Hesaplama Araçları</a>
        <div class="submenu">
            <a href="/accounting/hesaplama_araclari/kdv_hesaplama.php" class="<?= $current_page == 'kdv_hesaplama.php' ? 'active' : '' ?>">KDV Hesaplama</a>
            <a href="/accounting/hesaplama_araclari/taksit_hesaplama.php" class="<?= $current_page == 'taksit_hesaplama.php' ? 'active' : '' ?>">Taksit Hesaplama</a>
            <a href="/accounting/hesaplama_araclari/kar_zarar_analizi.php" class="<?= $current_page == 'kar_zarar_analizi.php' ? 'active' : '' ?>">Kar/Zarar Analizi</a>
            <a href="/accounting/hesaplama_araclari/doviz_cevirici.php" class="<?= $current_page == 'doviz_cevirici.php' ? 'active' : '' ?>">Döviz Çevirici</a>
            <a href="/accounting/hesaplama_araclari/canli_doviz.php" class="<?= $current_page == 'canli_doviz.php' ? 'active' : '' ?>">Canlı Döviz Kurları</a>
        </div>
        <a href="/accounting/logout.php" class="text-danger">Çıkış Yap</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
