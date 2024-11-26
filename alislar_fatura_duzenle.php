<?php
require_once 'header.php'; // Veritabanı bağlantısı ve kullanıcı doğrulama

// Düzenlenecek fatura kaydını seçme
if (isset($_GET['id'])) {
    $faturaId = $_GET['id'];

    // Mevcut fatura bilgilerini çekme
    $stmt = $conn->prepare("SELECT musteri_adi, fatura_no, miktar, odenen FROM faturalar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $faturaId);
        $stmt->execute();
        $stmt->bind_result($musteriAdi, $faturaNo, $miktar, $odenen);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Fatura bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz fatura ID'si.");
}

// Fatura bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['odenen'])) {
    $odenen = $_POST['odenen'];

    $stmt = $conn->prepare("UPDATE faturalar SET odenen = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("di", $odenen, $faturaId);
        $stmt->execute();
        $stmt->close();

        header("Location: alislar_fatura.php?id=" . $faturaId);
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
                            <label for="musteriAdi" class="form-label">Müşteri Adı</label>
                            <input type="text" class="form-control" id="musteriAdi" value="<?php echo htmlspecialchars($musteriAdi); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="faturaNo" class="form-label">Fatura No</label>
                            <input type="text" class="form-control" id="faturaNo" value="<?php echo htmlspecialchars($faturaNo); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="faturaTutar" class="form-label">Fatura Tutarı</label>
                            <input type="text" class="form-control" id="faturaTutar" value="<?php echo htmlspecialchars($miktar); ?> ₺" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="odenenTutar" class="form-label">Ödenen Tutar</label>
                            <input type="number" class="form-control" name="odenen" id="odenenTutar" value="<?php echo htmlspecialchars($odenen); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="alislar_fatura.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
