<?php
require_once 'header.php'; // Veritabanı bağlantısı ve kullanıcı doğrulama

// Düzenlenecek satış kaydını seçme
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mevcut satış bilgilerini çekme
    $stmt = $conn->prepare("SELECT baslik, urun_adi, birim_fiyati, miktar, musteri_id, fatura_no FROM satis WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($baslik, $urun_adi, $birim_fiyati, $miktar, $musteri_id, $fatura_no);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Satış bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz satış ID'si.");
}

// Satış bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['baslik'], $_POST['urun_adi'], $_POST['birim_fiyati'], $_POST['miktar'], $_POST['musteri_id'], $_POST['fatura_no'])) {
    $baslik = $_POST['baslik'];
    $urun_adi = $_POST['urun_adi'];
    $birim_fiyati = $_POST['birim_fiyati'];
    $miktar = $_POST['miktar'];
    $musteri_id = $_POST['musteri_id'];
    $fatura_no = $_POST['fatura_no'];

    $stmt = $conn->prepare("UPDATE satis SET baslik = ?, urun_adi = ?, birim_fiyati = ?, miktar = ?, musteri_id = ?, fatura_no = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssdissi", $baslik, $urun_adi, $birim_fiyati, $miktar, $musteri_id, $fatura_no, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: satis.php");
        exit();
    } else {
        die("Satış güncelleme hatası: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış Düzenle</title>
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
            <h2 class="mt-4 mb-3">Satış Düzenle</h2>

            <!-- Satış Düzenleme Formu -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="baslik" class="form-label">Satış Başlığı</label>
                            <input type="text" class="form-control" id="baslik" name="baslik" value="<?php echo htmlspecialchars($baslik); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="urunAdi" class="form-label">Ürün Adı</label>
                            <input type="text" class="form-control" id="urunAdi" name="urun_adi" value="<?php echo htmlspecialchars($urun_adi); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="birimFiyati" class="form-label">Birim Fiyatı</label>
                            <input type="number" step="0.01" class="form-control" id="birimFiyati" name="birim_fiyati" value="<?php echo htmlspecialchars($birim_fiyati); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="miktar" class="form-label">Miktar</label>
                            <input type="number" class="form-control" id="miktar" name="miktar" value="<?php echo htmlspecialchars($miktar); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="musteriSec" class="form-label">Müşteri</label>
                            <select name="musteri_id" id="musteriSec" class="form-select" required>
                                <?php
                                $musteriQuery = "SELECT id, ad_soyad FROM musteriler";
                                $musteriResult = $conn->query($musteriQuery);
                                if ($musteriResult->num_rows > 0) {
                                    while ($musteriRow = $musteriResult->fetch_assoc()) {
                                        $selected = $musteriRow['id'] == $musteri_id ? "selected" : "";
                                        echo "<option value=\"{$musteriRow['id']}\" $selected>{$musteriRow['ad_soyad']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="faturaNo" class="form-label">Fatura No</label>
                            <input type="text" class="form-control" id="faturaNo" name="fatura_no" value="<?php echo htmlspecialchars($fatura_no); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="satis.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
