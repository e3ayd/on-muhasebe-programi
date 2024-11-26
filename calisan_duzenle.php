<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

// Düzenlenecek çalışanı seçme
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mevcut çalışan bilgilerini çekme
    $stmt = $conn->prepare("SELECT ad, pozisyon, maas, avans, ekstra_odeme1, ekstra_odeme2 FROM calisanlar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($ad, $pozisyon, $maas, $avans, $ekstra_odeme1, $ekstra_odeme2);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Çalışan bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz çalışan ID'si.");
}

// Çalışan bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad'], $_POST['pozisyon'], $_POST['maas'])) {
    $ad = $_POST['ad'];
    $pozisyon = $_POST['pozisyon'];
    $maas = $_POST['maas'];
    $avans = $_POST['avans'] ?? 0;
    $ekstra_odeme1 = $_POST['ekstra_odeme1'] ?? 0;
    $ekstra_odeme2 = $_POST['ekstra_odeme2'] ?? 0;

    $stmt = $conn->prepare("UPDATE calisanlar SET ad = ?, pozisyon = ?, maas = ?, avans = ?, ekstra_odeme1 = ?, ekstra_odeme2 = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssddddi", $ad, $pozisyon, $maas, $avans, $ekstra_odeme1, $ekstra_odeme2, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: calisanlar.php");
        exit();
    } else {
        die("Çalışan güncelleme hatası: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışan Düzenle</title>
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
            <h2 class="mt-4 mb-3">Çalışan Düzenle</h2>

            <!-- Çalışan Düzenleme Formu -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="ad" class="form-label">Ad</label>
                            <input type="text" class="form-control" id="ad" name="ad" value="<?php echo htmlspecialchars($ad); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="pozisyon" class="form-label">Pozisyon</label>
                            <input type="text" class="form-control" id="pozisyon" name="pozisyon" value="<?php echo htmlspecialchars($pozisyon); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="maas" class="form-label">Maaş</label>
                            <input type="number" class="form-control" id="maas" name="maas" value="<?php echo htmlspecialchars($maas); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="avans" class="form-label">Avans</label>
                            <input type="number" class="form-control" id="avans" name="avans" value="<?php echo htmlspecialchars($avans); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="ekstra_odeme1" class="form-label">Ekstra Ödeme 1</label>
                            <input type="number" class="form-control" id="ekstra_odeme1" name="ekstra_odeme1" value="<?php echo htmlspecialchars($ekstra_odeme1); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="ekstra_odeme2" class="form-label">Ekstra Ödeme 2</label>
                            <input type="number" class="form-control" id="ekstra_odeme2" name="ekstra_odeme2" value="<?php echo htmlspecialchars($ekstra_odeme2); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="calisanlar.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
