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

// Fatura Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['baslik'], $_POST['miktar'], $_POST['musteri_adi'], $_POST['fatura_no'], $_POST['odenen'])) {
    $baslik = $_POST['baslik'];
    $miktar = $_POST['miktar'];
    $musteriAdi = $_POST['musteri_adi'];
    $faturaNo = $_POST['fatura_no'];
    $odenen = $_POST['odenen'];

    // Dosya Yükleme
    $dosyaAdi = basename($_FILES['fatura_dosyasi']['name']);
    $dosyaYolu = "uploads/" . $dosyaAdi;
    move_uploaded_file($_FILES['fatura_dosyasi']['tmp_name'], $dosyaYolu);

    $stmt = $conn->prepare("INSERT INTO faturalar (musteri_adi, baslik, miktar, fatura_no, odenen, dosya) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssdsds", $musteriAdi, $baslik, $miktar, $faturaNo, $odenen, $dosyaYolu);
        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Fatura başarıyla eklendi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Fatura eklenirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Fatura ekleme işlemi başlatılamadı!'
        ];
    }
    header("Location: faturalar.php");
    exit();
}

// Fatura Silme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = intval($_POST['sil_id']); // Güvenlik için intval kullanımı

    $stmt = $conn->prepare("DELETE FROM faturalar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $sil_id);
        if ($stmt->execute()) {
            // ID sıralama işlemi
            $conn->query("SET @new_id = 0;");
            $conn->query("UPDATE faturalar SET id = (@new_id := @new_id + 1);");
            $conn->query("ALTER TABLE faturalar AUTO_INCREMENT = 1;");

            $_SESSION['notification'] = [
                'type' => 'danger', // Kırmızı bildirim (silme işlemi)
                'message' => 'Fatura başarıyla silindi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Fatura silinirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Fatura silme işlemi başlatılamadı!'
        ];
    }
    header("Location: faturalar.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faturalar</title>
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
            <h2 class="mt-4 mb-3">Faturalar</h2>
            
            <!-- Fatura Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Fatura Ekle
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="faturaBasligi" class="form-label">Fatura Başlığı</label>
                            <input type="text" class="form-control" name="baslik" placeholder="Fatura Başlığı" required>
                        </div>
                        <div class="mb-3">
                            <label for="musteriSec" class="form-label">Müşteri Seç</label>
                            <select name="musteri_adi" id="musteriSec" class="form-select" required>
                                <?php
                                $musteriQuery = "SELECT ad_soyad FROM musteriler";
                                $musteriResult = $conn->query($musteriQuery);
                                if ($musteriResult->num_rows > 0) {
                                    while ($musteriRow = $musteriResult->fetch_assoc()) {
                                        echo "<option value=\"{$musteriRow['ad_soyad']}\">{$musteriRow['ad_soyad']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="faturaNo" class="form-label">Fatura No</label>
                            <input type="text" class="form-control" name="fatura_no" placeholder="Fatura No" required>
                        </div>
                        <div class="mb-3">
                            <label for="faturaMiktari" class="form-label">Fatura Miktarı</label>
                            <input type="number" step="0.01" class="form-control" name="miktar" placeholder="Fatura Miktarı" required>
                        </div>
                        <div class="mb-3">
                            <label for="odenenMiktar" class="form-label">Ödenen Miktar</label>
                            <input type="number" step="0.01" class="form-control" name="odenen" placeholder="Ödenen Miktar" required>
                        </div>
                        <div class="mb-3">
                            <label for="faturaDosyasi" class="form-label">Fatura Dosyası</label>
                            <input type="file" class="form-control" name="fatura_dosyasi" accept="image/*,application/pdf" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Fatura Ekle</button>
                    </form>
                </div>
            </div>

            <!-- Fatura Listesi -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Fatura Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Başlık</th>
                                <th>Müşteri</th>
                                <th>Fatura No</th>
                                <th>Miktar</th>
                                <th>Ödenen</th>
                                <th>Dosya</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM faturalar ORDER BY created_at ASC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['baslik']}</td>
                                        <td>{$row['musteri_adi']}</td>
                                        <td>{$row['fatura_no']}</td>
                                        <td>{$row['miktar']} ₺</td>
                                        <td>{$row['odenen']} ₺</td>
                                        <td><a href='{$row['dosya']}' target='_blank'>Dosyayı Görüntüle</a></td>
                                        <td>{$row['created_at']}</td>
                                        <td>
                                            <a href='fatura_duzenle.php?id={$row['id']}' class='btn btn-sm btn-primary'>Düzenle</a>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu faturayı silmek istediğinize emin misiniz?\");'>Sil</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>Fatura bulunamadı.</td></tr>";
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
