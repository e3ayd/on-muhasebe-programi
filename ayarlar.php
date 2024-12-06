<?php
require_once 'header.php'; // Header ve veritabanı bağlantısı


// Bildirim gösterimi (Bildirim varsa gösterilir ve ardından temizlenir)
if (isset($_SESSION['notification'])) {
    $type = htmlspecialchars($_SESSION['notification']['type']); // success, danger
    $message = htmlspecialchars($_SESSION['notification']['message']);
    echo "
    <div class='notification $type'>
        <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
        <p>$message</p>
    </div>";
    unset($_SESSION['notification']); // Bildirimi gösterdikten sonra temizle
}

// Admin Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['sifre'])) {
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];
    $izin = 'admin';

    // Şifreyi güvenli bir şekilde hashle
    $hashedPassword = password_hash($sifre, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO kullanicilar (email, sifre, izin) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $email, $hashedPassword, $izin);
        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Yeni admin başarıyla eklendi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Hata: Admin eklenemedi.'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Hata: İşlem gerçekleştirilemedi!'
        ];
    }
    header("Location: ayarlar.php");
    exit();
}

// Admin Silme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM kullanicilar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
            // ID'leri yeniden sıralamak için sorgular
            $conn->query("SET @new_id = 0;");
            $conn->query("UPDATE kullanicilar SET id = (@new_id := @new_id + 1);");
            $conn->query("ALTER TABLE kullanicilar AUTO_INCREMENT = 1;");

            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Admin başarıyla silindi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Hata: Admin silinemedi!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Hata: İşlem gerçekleştirilemedi!'
        ];
    }
    header("Location: ayarlar.php");
    exit();
}
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
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #fefefe;
            border-left: 5px solid;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            color: #333;
            z-index: 1000;
            min-width: 300px;
            animation: slideIn 0.4s ease;
        }
        .notification.success {
            border-color: #4caf50;
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .notification.danger {
            border-color: #f44336;
            background-color: #ffebee;
            color: #c62828;
        }
        .notification .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            color: #888;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .notification .close-btn:hover {
            color: #000;
        }
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
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
                <div class="card-header bg-dark text-white">Admin Listesi</div>
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
                            $query = "SELECT id, email, izin FROM kullanicilar WHERE izin = 'admin' ORDER BY id ASC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['izin']}</td>
                                        <td>
                                            <a href='ayarlar_duzenle.php?id={$row['id']}' class='btn btn-sm btn-primary'>Düzenle</a>
                                            <form method='POST' class='d-inline'>
                                                <input type='hidden' name='delete_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu kullanıcıyı silmek istediğinize emin misiniz?\");'>Sil</button>
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
</body>
</html>
