<?php
require_once 'header.php'; // Veritabanı bağlantısı ve kullanıcı doğrulama

// Düzenlenecek gelir kaydını seçme
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mevcut gelir bilgilerini çekme
    $stmt = $conn->prepare("SELECT aciklama, miktar, tarih FROM gelirler WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($aciklama, $miktar, $tarih);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Gelir bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz gelir ID'si.");
}

// Gelir bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aciklama'], $_POST['miktar'], $_POST['tarih'])) {
    $aciklama = $_POST['aciklama'];
    $miktar = $_POST['miktar'];
    $tarih = $_POST['tarih'];

    $stmt = $conn->prepare("UPDATE gelirler SET aciklama = ?, miktar = ?, tarih = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("sdsi", $aciklama, $miktar, $tarih, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: gelirler.php");
        exit();
    } else {
        die("Gelir güncelleme hatası: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gelir Düzenle</title>
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
            <h2 class="mt-4 mb-3">Gelir Düzenle</h2>

            <!-- Gelir Düzenleme Formu -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="aciklama" class="form-label">Açıklama</label>
                            <input type="text" class="form-control" id="aciklama" name="aciklama" value="<?php echo htmlspecialchars($aciklama); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="miktar" class="form-label">Miktar</label>
                            <input type="number" class="form-control" id="miktar" name="miktar" value="<?php echo htmlspecialchars($miktar); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="tarih" class="form-label">Tarih</label>
                            <input type="date" class="form-control" id="tarih" name="tarih" value="<?php echo htmlspecialchars($tarih); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="gelirler.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
