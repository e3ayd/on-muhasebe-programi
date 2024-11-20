<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar</title>
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
            <h2 class="mt-4 mb-3">Ayarlar - Admin Yönetimi</h2>

            <!-- Yeni Admin Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Admin Ekle
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">Admin E-posta</label>
                            <input type="email" class="form-control" name="email" placeholder="Admin E-posta" required>
                        </div>
                        <div class="mb-3">
                            <label for="adminSifre" class="form-label">Şifre</label>
                            <input type="password" class="form-control" name="sifre" placeholder="Şifre" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Admin Ekle</button>
                    </form>
                </div>
            </div>

            <!-- Admin Listesi -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Admin Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>E-posta</th>
                                <th>İzin</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT id, email, izin FROM kullanicilar WHERE izin = 'admin'";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['izin']}</td>
                                        <td>
                                            <form method='POST' class='d-inline'>
                                                <input type='hidden' name='delete_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger'>Sil</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Henüz admin eklenmedi.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Admin Ekleme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['sifre'])) {
        $email = $_POST['email'];
        $sifre = $_POST['sifre'];
        $izin = 'admin';

        // Şifreyi güvenli bir şekilde hashle
        $hashedPassword = password_hash($sifre, PASSWORD_BCRYPT);

        // Yeni admin ekleme
        $stmt = $conn->prepare("INSERT INTO kullanicilar (email, sifre, izin) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $izin);

        if ($stmt->execute()) {
            echo "<script>alert('Yeni admin başarıyla eklendi!'); window.location.href='ayarlar.php';</script>";
        } else {
            echo "<script>alert('Hata: Admin eklenemedi.');</script>";
        }

        $stmt->close();
    }

    // Admin Silme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];

        $stmt = $conn->prepare("DELETE FROM kullanicilar WHERE id = ?");
        $stmt->bind_param("i", $deleteId);

        if ($stmt->execute()) {
            echo "<script>alert('Admin başarıyla silindi!'); window.location.href='ayarlar.php';</script>";
        } else {
            echo "<script>alert('Hata: Admin silinemedi.');</script>";
        }

        $stmt->close();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
