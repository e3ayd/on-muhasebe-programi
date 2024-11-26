<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

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
    $stmt->bind_param("ssdsds", $musteriAdi, $baslik, $miktar, $faturaNo, $odenen, $dosyaYolu);
    $stmt->execute();
    $stmt->close();

    header("Location: faturalar.php");
    exit();
}
?>
<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

// Fatura silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = $_POST['sil_id'];

    // Faturayı sil
    $stmt = $conn->prepare("DELETE FROM faturalar WHERE id = ?");
    $stmt->bind_param("i", $sil_id);
    $stmt->execute();
    $stmt->close();

    // Kalan faturaları yeniden sıralamak için
    $conn->query("SET @new_id = 0;"); // Yeni ID değerini sıfırla
    $conn->query("UPDATE faturalar SET id = (@new_id := @new_id + 1);"); // ID'leri sırayla güncelle
    $conn->query("ALTER TABLE faturalar AUTO_INCREMENT = 1;"); // AUTO_INCREMENT değerini sıfırla

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