<?php
require_once 'header.php'; // Veritabanı bağlantısı ve kullanıcı doğrulama

// Düzenlenecek admin bilgilerini çekme
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT email FROM kullanicilar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Admin bilgileri alınamadı: " . $conn->error);
    }
} else {
    die("Geçersiz admin ID'si.");
}

// Admin bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['sifre'])) {
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    // Şifreyi hashle
    $hashedPassword = password_hash($sifre, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("UPDATE kullanicilar SET email = ?, sifre = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssi", $email, $hashedPassword, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Admin bilgileri başarıyla güncellendi!'); window.location.href='ayarlar.php';</script>";
        } else {
            echo "<script>alert('Hata: Admin bilgileri güncellenemedi.');</script>";
        }
        $stmt->close();
    } else {
        die("Hata: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Admin Düzenle</title>
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
            <h2 class="mt-4 mb-3">Admin Düzenle</h2>

            <!-- Admin Düzenleme Formu -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Admin E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="sifre" class="form-label">Yeni Şifre</label>
                            <input type="password" class="form-control" id="sifre" name="sifre" placeholder="Yeni Şifre" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="ayarlar.php" class="btn btn-secondary">İptal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
