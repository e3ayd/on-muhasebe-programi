<?php
require_once 'header.php'; // Veritabanı bağlantısını içerdiğinden emin olun

// Düzenlenecek müşteriyi seçme
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mevcut müşteri bilgilerini çekme
    $stmt = $conn->prepare("SELECT ad_soyad, telefon, firma_adi, email FROM musteriler WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($ad_soyad, $telefon, $firma_adi, $email);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Müşteri bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz müşteri ID'si.");
}

// Müşteri bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad_soyad'], $_POST['telefon'], $_POST['firma_adi'], $_POST['email'])) {
    $ad_soyad = $_POST['ad_soyad'];
    $telefon = $_POST['telefon'];
    $firma_adi = $_POST['firma_adi'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE musteriler SET ad_soyad = ?, telefon = ?, firma_adi = ?, email = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssssi", $ad_soyad, $telefon, $firma_adi, $email, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: musteri.php");
        exit();
    } else {
        die("Müşteri güncelleme hatası: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Düzenle</title>
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
            <h2 class="mt-4 mb-3">Müşteri Düzenle</h2>

            <!-- Müşteri Düzenleme Formu -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="ad_soyad" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" value="<?php echo htmlspecialchars($ad_soyad); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefon" class="form-label">Telefon</label>
                            <input type="text" class="form-control" id="telefon" name="telefon" value="<?php echo htmlspecialchars($telefon); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="firma_adi" class="form-label">Firma Adı</label>
                            <input type="text" class="form-control" id="firma_adi" name="firma_adi" value="<?php echo htmlspecialchars($firma_adi); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="musteri.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
