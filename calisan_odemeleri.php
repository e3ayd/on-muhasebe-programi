<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama
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
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
            <h2 class="mt-4 mb-3">Çalışan Ödemeleri</h2>

            <!-- Aylık Toplam -->
            <div class="alert alert-info">
                <strong>Bu ay toplam ödeme:</strong> <span id="aylikToplam">0</span> ₺
            </div>

            <!-- Ay Seçimi -->
            <div class="mb-4">
                <label for="aySecimi" class="form-label">Ay Seçimi:</label>
                <select id="aySecimi" class="form-select" onchange="filtreleAy()">
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
                                <th>#</th>
                                <th>Çalışan Adı</th>
                                <th>Tarih</th>
                                <th>Ödeme Miktarı</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $currentMonth = date("Y-m");
                            $odemeQuery = "SELECT o.id, c.ad AS calisan_adi, o.tarih, o.miktar 
                                           FROM calisan_odeme o 
                                           INNER JOIN calisanlar c ON o.calisan_id = c.id 
                                           WHERE DATE_FORMAT(o.tarih, '%Y-%m') = '$currentMonth'";
                            $odemeResult = $conn->query($odemeQuery);

                            if ($odemeResult->num_rows > 0) {
                                while ($odeme = $odemeResult->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$odeme['id']}</td>
                                        <td>{$odeme['calisan_adi']}</td>
                                        <td>{$odeme['tarih']}</td>
                                        <td>{$odeme['miktar']} ₺</td>
                                        <td>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$odeme['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger'>Sil</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Kayıt bulunamadı.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Ödeme Ekleme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calisan_id'], $_POST['tarih'], $_POST['miktar'])) {
        $calisan_id = $_POST['calisan_id'];
        $tarih = $_POST['tarih'];
        $miktar = $_POST['miktar'];

        $stmt = $conn->prepare("INSERT INTO calisan_odeme (calisan_id, miktar, tarih) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $calisan_id, $miktar, $tarih);
        $stmt->execute();
        $stmt->close();

        header("Location: calisan_odemeleri.php");
        exit();
    }

    // Ödeme Silme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
        $sil_id = $_POST['sil_id'];

        $stmt = $conn->prepare("DELETE FROM calisan_odeme WHERE id = ?");
        $stmt->bind_param("i", $sil_id);
        $stmt->execute();
        $stmt->close();

        header("Location: calisan_odemeleri.php");
        exit();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
