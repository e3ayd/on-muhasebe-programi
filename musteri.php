<?php
require_once 'header.php'; // Header ve veritabanı bağlantısı

// Yeni Müşteri Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad_soyad'], $_POST['telefon'], $_POST['email'], $_POST['firma_adi'])) {
    $ad_soyad = $_POST['ad_soyad'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];
    $firma_adi = $_POST['firma_adi'];

    $stmt = $conn->prepare("INSERT INTO musteriler (ad_soyad, telefon, email, firma_adi) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $ad_soyad, $telefon, $email, $firma_adi);
        $stmt->execute();
        $stmt->close();

        header("Location: musteri.php"); // Yönlendirme yaparken HTML çıktısı olmamalı
        exit();
    } else {
        die("Müşteri ekleme hatası: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteriler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
            <h2 class="mt-4 mb-3">Müşteriler</h2>
            
            <!-- Müşteri Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Müşteri Ekle
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="ad_soyad" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefon" class="form-label">Telefon</label>
                            <input type="text" class="form-control" id="telefon" name="telefon" required>
                        </div>
                        <div class="mb-3">
                            <label for="firma_adi" class="form-label">Firma Adı</label>
                            <input type="text" class="form-control" id="firma_adi" name="firma_adi" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </form>
                </div>
            </div>

                        <!-- Müşteri Listesi -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Müşteri Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ad Soyad</th>
                                <th>Telefon</th>
                                <th>Firma Adı</th>
                                <th>E-posta</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Müşterileri veritabanından çekme
                            $stmt = $conn->prepare("SELECT id, ad_soyad, telefon, firma_adi, email FROM musteriler");
                            if ($stmt) {
                                $stmt->execute();
                                $stmt->bind_result($id, $ad_soyad, $telefon, $firma_adi, $email);

                                while ($stmt->fetch()) {
                                    echo "<tr>
                                            <td>{$id}</td>
                                            <td>{$ad_soyad}</td>
                                            <td>{$telefon}</td>
                                            <td>{$firma_adi}</td>
                                            <td>{$email}</td>
                                            <td>
                                                <a href='musteri_duzenle.php?id={$id}' class='btn btn-sm btn-primary'>Düzenle</a>
                                                <a href='musteri_sil.php?id={$id}' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu müşteriyi silmek istediğinize emin misiniz?\");'>Sil</a>
                                            </td>
                                        </tr>";
                                }
                                $stmt->close();
                            } else {
                                die("SQL hazırlama hatası: " . $conn->error);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>  
