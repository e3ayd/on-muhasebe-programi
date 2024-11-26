<?php
require_once 'db.php'; // Veritabanı bağlantı dosyası

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];
    $izin = 'admin'; // İzin varsayılan olarak admin

    if (!empty($email) && !empty($sifre)) {
        // Şifreyi güvenli bir şekilde hashle
        $hashedPassword = password_hash($sifre, PASSWORD_BCRYPT);

        // Kullanıcıyı veritabanına ekle
        $stmt = $conn->prepare("INSERT INTO kullanicilar (email, sifre, izin) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $izin);

        if ($stmt->execute()) {
            echo "Kullanıcı başarıyla kaydedildi!";
        } else {
            echo "Hata: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Lütfen tüm alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Ekle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Yeni Kullanıcı Ekle</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-posta</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="E-posta adresi" required>
            </div>
            <div class="mb-3">
                <label for="sifre" class="form-label">Şifre</label>
                <input type="password" class="form-control" name="sifre" id="sifre" placeholder="Şifre" required>
            </div>
            <button type="submit" class="btn btn-primary">Kullanıcı Ekle</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
