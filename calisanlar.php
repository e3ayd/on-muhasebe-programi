<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

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

// Çalışan Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calisan_adi'])) {
    $ad = $_POST['calisan_adi'];
    $pozisyon = $_POST['pozisyon'];
    $maas = $_POST['maas'];
    $avans = $_POST['avans'] ?? 0;
    $ekstra_odeme1 = $_POST['ekstra_odeme1'] ?? 0;
    $ekstra_odeme2 = $_POST['ekstra_odeme2'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO calisanlar (ad, pozisyon, maas, avans, ekstra_odeme1, ekstra_odeme2) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssdddd", $ad, $pozisyon, $maas, $avans, $ekstra_odeme1, $ekstra_odeme2);
        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Çalışan başarıyla eklendi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Çalışan eklenirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Çalışan ekleme işlemi başlatılamadı!'
        ];
    }
    header("Location: calisanlar.php");
    exit();
}

// Çalışan Silme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = intval($_POST['sil_id']); // Güvenlik için intval kullanımı

    $stmt = $conn->prepare("DELETE FROM calisanlar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $sil_id);
        if ($stmt->execute()) {
            // ID sıralama işlemi
            $conn->query("SET @new_id = 0;");
            $conn->query("UPDATE calisanlar SET id = (@new_id := @new_id + 1);");
            $conn->query("ALTER TABLE calisanlar AUTO_INCREMENT = 1;");

            $_SESSION['notification'] = [
                'type' => 'danger', 
                'message' => 'Çalışan başarıyla silindi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Çalışan silinirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Çalışan silme işlemi başlatılamadı!'
        ];
    }
    header("Location: calisanlar.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışanlar</title>
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
            <h2 class="mt-4 mb-3">Çalışanlar</h2>

            <!-- Çalışan Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Çalışan Ekle
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="calisanAdi" class="form-label">Çalışan Adı</label>
                            <input type="text" class="form-control" name="calisan_adi" placeholder="Çalışan Adı" required>
                        </div>
                        <div class="mb-3">
                            <label for="calisanPozisyon" class="form-label">Pozisyon</label>
                            <input type="text" class="form-control" name="pozisyon" placeholder="Pozisyon" required>
                        </div>
                        <div class="mb-3">
                            <label for="calisanMaas" class="form-label">Maaş</label>
                            <input type="number" class="form-control" name="maas" placeholder="Maaş" required>
                        </div>
                        <div class="mb-3">
                            <label for="calisanAvans" class="form-label">Avans</label>
                            <input type="number" class="form-control" name="avans" placeholder="Avans">
                        </div>
                        <div class="mb-3">
                            <label for="ekstraOdeme1" class="form-label">Ekstra Ödeme 1</label>
                            <input type="number" class="form-control" name="ekstra_odeme1" placeholder="Ekstra Ödeme 1">
                        </div>
                        <div class="mb-3">
                            <label for="ekstraOdeme2" class="form-label">Ekstra Ödeme 2</label>
                            <input type="number" class="form-control" name="ekstra_odeme2" placeholder="Ekstra Ödeme 2">
                        </div>
                        <button type="submit" class="btn btn-primary">Çalışan Ekle</button>
                    </form>
                </div>
            </div>

            <!-- Çalışan Listesi -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Çalışan Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adı</th>
                                <th>Pozisyon</th>
                                <th>Maaş</th>
                                <th>Avans</th>
                                <th>Ekstra Ödeme 1</th>
                                <th>Ekstra Ödeme 2</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM calisanlar ORDER BY id ASC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['ad']}</td>
                                        <td>{$row['pozisyon']}</td>
                                        <td>{$row['maas']} ₺</td>
                                        <td>{$row['avans']} ₺</td>
                                        <td>{$row['ekstra_odeme1']} ₺</td>
                                        <td>{$row['ekstra_odeme2']} ₺</td>
                                         <td>
                                            <a href='calisan_duzenle.php?id={$row['id']}' class='btn btn-sm btn-primary'>Düzenle</a>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu çalışanı silmek istediğinize emin misiniz?\");'>Sil</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>Kayıt bulunamadı.</td></tr>";
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
