<?php
require_once 'header.php'; // Veritabanı bağlantısını içerdiğinden emin olun

// Düzenlenecek ödeme kaydını seçme
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mevcut ödeme bilgilerini çekme
    $stmt = $conn->prepare("SELECT calisan_id, tarih, miktar FROM calisan_odeme WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($calisan_id, $tarih, $miktar);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Ödeme bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz ödeme ID'si.");
}

// Ödeme bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calisan_id'], $_POST['tarih'], $_POST['miktar'])) {
    $calisan_id = $_POST['calisan_id'];
    $tarih = $_POST['tarih'];
    $miktar = $_POST['miktar'];

    $stmt = $conn->prepare("UPDATE calisan_odeme SET calisan_id = ?, tarih = ?, miktar = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("isdi", $calisan_id, $tarih, $miktar, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: calisan_odemeleri.php");
        exit();
    } else {
        die("Ödeme güncelleme hatası: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Düzenle</title>
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
            <h2 class="mt-4 mb-3">Ödeme Düzenle</h2>

            <!-- Ödeme Düzenleme Formu -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="calisan_id" class="form-label">Çalışan Seçimi</label>
                            <select id="calisan_id" name="calisan_id" class="form-select" required>
                                <?php
                                $calisanQuery = "SELECT id, ad FROM calisanlar";
                                $calisanResult = $conn->query($calisanQuery);

                                while ($calisan = $calisanResult->fetch_assoc()) {
                                    $selected = $calisan_id == $calisan['id'] ? "selected" : "";
                                    echo "<option value='{$calisan['id']}' $selected>{$calisan['ad']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tarih" class="form-label">Tarih</label>
                            <input type="date" class="form-control" id="tarih" name="tarih" value="<?php echo htmlspecialchars($tarih); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="miktar" class="form-label">Ödeme Miktarı</label>
                            <input type="number" class="form-control" id="miktar" name="miktar" value="<?php echo htmlspecialchars($miktar); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="calisan_odemeleri.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
