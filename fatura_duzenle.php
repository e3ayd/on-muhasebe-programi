<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

// Düzenlenecek faturayı seçme
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mevcut fatura bilgilerini çekme
    $stmt = $conn->prepare("SELECT baslik, musteri_adi, fatura_no, miktar, odenen, dosya FROM faturalar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($baslik, $musteri_adi, $fatura_no, $miktar, $odenen, $dosya);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Fatura bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz fatura ID'si.");
}

// Fatura bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baslik = $_POST['baslik'];
    $musteri_adi = $_POST['musteri_adi'];
    $fatura_no = $_POST['fatura_no'];
    $miktar = $_POST['miktar'];
    $odenen = $_POST['odenen'];

    // Dosya güncelleme işlemleri buraya eklenebilir

    $stmt = $conn->prepare("UPDATE faturalar SET baslik = ?, musteri_adi = ?, fatura_no = ?, miktar = ?, odenen = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssdddi", $baslik, $musteri_adi, $fatura_no, $miktar, $odenen, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: faturalar.php");
        exit();
    } else {
        die("Fatura güncelleme hatası: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura Düzenle</title>
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
            <h2 class="mt-4 mb-3">Fatura Düzenle</h2>

            <!-- Fatura Düzenleme Formu -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="baslik" class="form-label">Başlık</label>
                            <input type="text" class="form-control" id="baslik" name="baslik" value="<?php echo htmlspecialchars($baslik); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="musteri_adi" class="form-label">Müşteri Adı</label>
                            <input type="text" class="form-control" id="musteri_adi" name="musteri_adi" value="<?php echo htmlspecialchars($musteri_adi); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="fatura_no" class="form-label">Fatura No</label>
                            <input type="text" class="form-control" id="fatura_no" name="fatura_no" value="<?php echo htmlspecialchars($fatura_no); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="miktar" class="form-label">Miktar</label>
                            <input type="number" step="0.01" class="form-control" id="miktar" name="miktar" value="<?php echo htmlspecialchars($miktar); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="odenen" class="form-label">Ödenen</label>
                            <input type="number" step="0.01" class="form-control" id="odenen" name="odenen" value="<?php echo htmlspecialchars($odenen); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="dosya" class="form-label">Dosya</label>
                            <input type="file" class="form-control" id="dosya" name="dosya">
                            <small class="form-text text-muted">Dosyayı güncellemek için yeni bir dosya seçin.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="faturalar.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>