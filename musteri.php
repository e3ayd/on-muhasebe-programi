<?php
require_once 'header.php'; // Header ve veritabanı bağlantısı

// Bildirim gösterimi (Bildirim varsa gösterilir ve ardından temizlenir)
if (isset($_SESSION['notification'])) {
    $type = htmlspecialchars($_SESSION['notification']['type']);
    $message = htmlspecialchars($_SESSION['notification']['message']);
    echo "
    <div class='notification $type'>
        <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
        <p>$message</p>
    </div>";
    unset($_SESSION['notification']); // Bildirimi gösterdikten sonra temizle
}

// Yeni Müşteri Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ad_soyad'], $_POST['telefon'], $_POST['email'], $_POST['firma_adi'])) {
    $ad_soyad = $_POST['ad_soyad'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];
    $firma_adi = $_POST['firma_adi'];

    $stmt = $conn->prepare("INSERT INTO musteriler (ad_soyad, telefon, email, firma_adi) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $ad_soyad, $telefon, $email, $firma_adi);
        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Müşteri başarıyla eklendi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Müşteri eklenirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Müşteri ekleme işlemi başlatılamadı!'
        ];
    }
    header("Location: musteri.php");
    exit();
}

// Müşteri Silme İşlemi
if (isset($_GET['sil_id'])) {
    $sil_id = intval($_GET['sil_id']); // Güvenlik için ID'yi sayıya dönüştür

    $stmt = $conn->prepare("DELETE FROM musteriler WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $sil_id);
        if ($stmt->execute()) {
            // ID sıralama işlemi
            $conn->query("SET @new_id = 0;");
            $conn->query("UPDATE musteriler SET id = (@new_id := @new_id + 1);");
            $conn->query("ALTER TABLE musteriler AUTO_INCREMENT = 1;");

            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Müşteri başarıyla silindi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Müşteri silinirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Müşteri silme işlemi başlatılamadı!'
        ];
    }
    header("Location: musteri.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteriler</title>
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
            <h2 class="mt-4 mb-3">Müşteriler</h2>
            
            <!-- Müşteri Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Müşteri Ekle
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="ad_soyad" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefon" class="form-label">Telefon</label>
                            <input type="text" class="form-control" id="telefon" name="telefon" required>
                        </div>
                        <div class="mb-3">
                            <label for="firma_adi" class="form-label">Firma Adı</label>
                            <input type="text" class="form-control" id="firma_adi" name="firma_adi" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </form>
                </div>
            </div>

            <!-- Müşteri Listesi -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Müşteri Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ad Soyad</th>
                                <th>Telefon</th>
                                <th>Firma Adı</th>
                                <th>E-posta</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT id, ad_soyad, telefon, firma_adi, email FROM musteriler");
                            if ($stmt) {
                                $stmt->execute();
                                $stmt->bind_result($id, $ad_soyad, $telefon, $firma_adi, $email);
                                while ($stmt->fetch()) {
                                    echo "<tr>
                                            <td>{$id}</td>
                                            <td>{$ad_soyad}</td>
                                            <td>{$telefon}</td>
                                            <td>{$firma_adi}</td>
                                            <td>{$email}</td>
                                            <td>
                                                <a href='musteri_duzenle.php?id={$id}' class='btn btn-sm btn-primary'>Düzenle</a>
                                                <a href='musteri.php?sil_id={$id}' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu müşteriyi silmek istediğinize emin misiniz?\");'>Sil</a>
                                            </td>
                                        </tr>";
                                }
                                $stmt->close();
                            } else {
                                die("SQL hazırlama hatası: " . $conn->error);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
