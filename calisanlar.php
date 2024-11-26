<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

// Çalışan Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calisan_adi'])) {
    $ad = $_POST['calisan_adi'];
    $pozisyon = $_POST['pozisyon'];
    $maas = $_POST['maas'];
    $avans = $_POST['avans'] ?? 0;
    $ekstra_odeme1 = $_POST['ekstra_odeme1'] ?? 0;
    $ekstra_odeme2 = $_POST['ekstra_odeme2'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO calisanlar (ad, pozisyon, maas, avans, ekstra_odeme1, ekstra_odeme2) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdddd", $ad, $pozisyon, $maas, $avans, $ekstra_odeme1, $ekstra_odeme2);
    $stmt->execute();
    $stmt->close();

    header("Location: calisanlar.php");
    exit();
}

// Çalışanı silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = $_POST['sil_id'];

    // Çalışanı sil
    $stmt = $conn->prepare("DELETE FROM calisanlar WHERE id = ?");
    $stmt->bind_param("i", $sil_id);
    $stmt->execute();
    $stmt->close();

    // Kalan çalışanları yeniden sıralamak için
    $conn->query("SET @new_id = 0;"); // Yeni ID değerini sıfırla
    $conn->query("UPDATE calisanlar SET id = (@new_id := @new_id + 1);"); // ID'leri sırayla güncelle
    $conn->query("ALTER TABLE calisanlar AUTO_INCREMENT = 1;"); // AUTO_INCREMENT değerini sıfırla

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
                        <button type="submit" class="btn btn-primary">Kaydet</button>
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
                                                <button type='submit' class='btn btn-sm btn-danger'  onclick='return confirm(\"Bu çalışanı silmek istediğinize emin misiniz?\");'>Sil</button>
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
