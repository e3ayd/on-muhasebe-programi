<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

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

// Aylık toplam ödeme hesaplama
$currentMonth = date("Y-m");
$aylikToplamSorgu = "SELECT SUM(miktar) AS toplam FROM calisan_odeme WHERE DATE_FORMAT(tarih, '%Y-%m') = '$currentMonth'";
$aylikToplamSonuc = $conn->query($aylikToplamSorgu);
$aylikToplam = $aylikToplamSonuc->fetch_assoc()['toplam'] ?? 0;

// Ödeme Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calisan_id'], $_POST['tarih'], $_POST['miktar'])) {
    $calisan_id = $_POST['calisan_id'];
    $tarih = $_POST['tarih'];
    $miktar = $_POST['miktar'];

    $stmt = $conn->prepare("INSERT INTO calisan_odeme (calisan_id, miktar, tarih) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ids", $calisan_id, $miktar, $tarih);
        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Ödeme başarıyla eklendi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Ödeme eklenirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Ödeme ekleme işlemi başlatılamadı!'
        ];
    }
    header("Location: calisan_odemeleri.php");
    exit();
}

// Ödeme Silme İşlemi ve ID'leri Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = intval($_POST['sil_id']); // Güvenlik için intval kullanımı

    $stmt = $conn->prepare("DELETE FROM calisan_odeme WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $sil_id);
        if ($stmt->execute()) {
            $conn->query("SET @new_id = 0;");
            $conn->query("UPDATE calisan_odeme SET id = (@new_id := @new_id + 1) ORDER BY id ASC;");
            $conn->query("ALTER TABLE calisan_odeme AUTO_INCREMENT = 1;");

            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Ödeme başarıyla silindi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Ödeme silinirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Ödeme silme işlemi başlatılamadı!'
        ];
    }
    header("Location: calisan_odemeleri.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışan Ödemeleri</title>
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
            <h2 class="mt-4 mb-3">Çalışan Ödemeleri</h2>

            <!-- Aylık Toplam -->
            <div class="alert alert-info">
                <strong>Bu ay toplam ödeme:</strong> <span id="aylikToplam"><?php echo number_format($aylikToplam, 2, ',', '.'); ?></span> ₺
            </div>

            <!-- Ay Seçimi -->
            <div class="mb-4">
                <label for="aySecimi" class="form-label">Ay Seçimi:</label>
                <select id="aySecimi" class="form-select">
                    <?php
                    for ($i = 0; $i < 12; $i++) {
                        $date = date("Y-m", strtotime("-$i months"));
                        echo "<option value='$date'>$date</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Ödeme Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Ödeme Ekle
                </div>
                <div class="card-body">
                    <form id="odemeEkleForm" method="POST">
                        <div class="mb-3">
                            <label for="calisanSecimi" class="form-label">Çalışan Adı</label>
                            <select id="calisanSecimi" class="form-select" name="calisan_id" required>
                                <?php
                                $calisanQuery = "SELECT id, ad FROM calisanlar";
                                $calisanResult = $conn->query($calisanQuery);

                                while ($calisan = $calisanResult->fetch_assoc()) {
                                    echo "<option value='{$calisan['id']}'>{$calisan['ad']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="odemeTarihi" class="form-label">Tarih</label>
                            <input type="date" class="form-control" name="tarih" id="odemeTarihi" required>
                        </div>
                        <div class="mb-3">
                            <label for="odemeMiktari" class="form-label">Ödeme Miktarı</label>
                            <input type="number" class="form-control" name="miktar" id="odemeMiktari" placeholder="Ödeme Miktarı" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </form>
                </div>
            </div>

            <!-- Ödeme Listesi -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Ödeme Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover" id="odemeListesi">
                        <thead>
                            <tr>
                                <th>Çalışan Adı</th>
                                <th>Tarih</th>
                                <th>Ödeme Miktarı</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $odemeQuery = "SELECT o.id, c.ad AS calisan_adi, o.tarih, o.miktar 
                                           FROM calisan_odeme o 
                                           INNER JOIN calisanlar c ON o.calisan_id = c.id 
                                           WHERE DATE_FORMAT(o.tarih, '%Y-%m') = '$currentMonth'";
                            $odemeResult = $conn->query($odemeQuery);

                            if ($odemeResult->num_rows > 0) {
                                while ($odeme = $odemeResult->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$odeme['calisan_adi']}</td>
                                        <td>{$odeme['tarih']}</td>
                                        <td>{$odeme['miktar']} ₺</td>
                                        <td>
                                            <a href='calisan_odeme_duzenle.php?id={$odeme['id']}' class='btn btn-sm btn-primary'>Düzenle</a>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$odeme['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu ödemeyi silmek istediğinize emin misiniz?\");'>Sil</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Kayıt bulunamadı.</td></tr>";
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
