<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

// Satış Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['baslik'], $_POST['urun_adi'], $_POST['birim_fiyati'], $_POST['miktar'], $_POST['musteri_id'], $_POST['fatura_no'])) {
    $baslik = $_POST['baslik'];
    $urun_adi = $_POST['urun_adi'];
    $birim_fiyati = $_POST['birim_fiyati'];
    $miktar = $_POST['miktar'];
    $musteri_id = $_POST['musteri_id'];
    $fatura_no = $_POST['fatura_no'];

    $stmt = $conn->prepare("INSERT INTO satis (baslik, urun_adi, birim_fiyati, miktar, musteri_id, fatura_no) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $baslik, $urun_adi, $birim_fiyati, $miktar, $musteri_id, $fatura_no);
    $stmt->execute();
    $stmt->close();

    header("Location: satis.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = $_POST['sil_id'];

    // Satışı sil
    $stmt = $conn->prepare("DELETE FROM satis WHERE id = ?");
    $stmt->bind_param("i", $sil_id);
    $stmt->execute();
    $stmt->close();

    // ID sıfırlama ve güncelleme
    $conn->query("SET @new_id = 0;"); // Geçici değişkeni sıfırla
    $conn->query("UPDATE satis SET id = (@new_id := @new_id + 1) ORDER BY id ASC;"); // ID'leri yeniden sırala

    // Mevcut maksimum ID'yi bul ve AUTO_INCREMENT değerini ayarla
    $result = $conn->query("SELECT MAX(id) AS max_id FROM satis;");
    $row = $result->fetch_assoc();
    $max_id = isset($row['max_id']) ? $row['max_id'] + 1 : 1; // Eğer tablo boşsa 1'e ayarla
    $conn->query("ALTER TABLE satis AUTO_INCREMENT = $max_id;"); // AUTO_INCREMENT değerini güncelle

    header("Location: satis.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satışlar</title>
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
            <h2 class="mt-4 mb-3">Satışlar</h2>

            <!-- Satış Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Satış Ekle
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="musteriSec" class="form-label">Müşteri Seç</label>
                            <select name="musteri_id" id="musteriSec" class="form-select" required>
                                <?php
                                $musteriQuery = "SELECT id, ad_soyad FROM musteriler";
                                $musteriResult = $conn->query($musteriQuery);
                                if ($musteriResult->num_rows > 0) {
                                    while ($musteriRow = $musteriResult->fetch_assoc()) {
                                        echo "<option value=\"{$musteriRow['id']}\">{$musteriRow['ad_soyad']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="satisBasligi" class="form-label">Satış Başlığı</label>
                            <input type="text" class="form-control" name="baslik" placeholder="Satış Başlığı" required>
                        </div>
                        <div class="mb-3">
                            <label for="urunAdi" class="form-label">Ürün Adı</label>
                            <input type="text" class="form-control" name="urun_adi" placeholder="Ürün Adı" required>
                        </div>
                        <div class="mb-3">
                            <label for="birimFiyati" class="form-label">Birim Fiyatı</label>
                            <input type="number" class="form-control" name="birim_fiyati" step="0.01" placeholder="Birim Fiyatı" required>
                        </div>
                        <div class="mb-3">
                            <label for="satisMiktari" class="form-label">Satış Miktarı</label>
                            <input type="number" class="form-control" name="miktar" placeholder="Satış Miktarı" required>
                        </div>
                        <div class="mb-3">
                            <label for="faturaNo" class="form-label">Fatura No</label>
                            <input type="text" class="form-control" name="fatura_no" placeholder="Fatura No" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Satış Kaydet</button>
                    </form>
                </div>
            </div>

            <!-- Satış Listesi -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Satış Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Müşteri</th>
                                <th>Başlık</th>
                                <th>Ürün Adı</th>
                                <th>Birim Fiyatı</th>
                                <th>Miktar</th>
                                <th>Fatura No</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT satis.id, satis.baslik, satis.urun_adi, satis.birim_fiyati, satis.miktar, musteriler.ad_soyad AS musteri_ad, satis.fatura_no, satis.created_at 
                                      FROM satis
                                      INNER JOIN musteriler ON satis.musteri_id = musteriler.id
                                      ORDER BY satis.created_at ASC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['musteri_ad']}</td>
                                        <td>{$row['baslik']}</td>
                                        <td>{$row['urun_adi']}</td>
                                        <td>{$row['birim_fiyati']} ₺</td>
                                        <td>{$row['miktar']}</td>
                                        <td>{$row['fatura_no']}</td>
                                        <td>{$row['created_at']}</td>
                                        <td>
                                            <a href='satis_duzenle.php?id={$row['id']}' class='btn btn-sm btn-primary'>Düzenle</a>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu satışı silmek istediğinize emin misiniz?\");'>Sil</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>Satış bulunamadı.</td></tr>";
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
