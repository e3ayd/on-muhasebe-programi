<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama
?>

<!DOCTYPE html>
<html lang="en">
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
                            <label for="satisBasligi" class="form-label">Satış Başlığı</label>
                            <input type="text" class="form-control" name="baslik" placeholder="Satış Başlığı" required>
                        </div>
                        <div class="mb-3">
                            <label for="satisMiktari" class="form-label">Satış Miktarı</label>
                            <input type="number" class="form-control" name="miktar" placeholder="Satış Miktarı" required>
                        </div>
                        <div class="mb-3">
                            <label for="musteri" class="form-label">Müşteri</label>
                            <select class="form-select" name="musteri_id" required>
                                <?php
                                $query = "SELECT id, ad FROM musteriler ORDER BY ad ASC";
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id']}'>{$row['ad']}</option>";
                                }
                                ?>
                            </select>
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
                                <th>Başlık</th>
                                <th>Miktar</th>
                                <th>Müşteri</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT satis.id, satis.baslik, satis.miktar, musteriler.ad AS musteri_ad, satis.created_at 
                                      FROM satis
                                      INNER JOIN musteriler ON satis.musteri_id = musteriler.id
                                      ORDER BY satis.created_at DESC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['baslik']}</td>
                                        <td>{$row['miktar']} ₺</td>
                                        <td>{$row['musteri_ad']}</td>
                                        <td>{$row['created_at']}</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Satış bulunamadı.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Satış Ekleme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['baslik'], $_POST['miktar'], $_POST['musteri_id'])) {
        $baslik = $_POST['baslik'];
        $miktar = $_POST['miktar'];
        $musteri_id = $_POST['musteri_id'];

        $stmt = $conn->prepare("INSERT INTO satis (baslik, miktar, musteri_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $baslik, $miktar, $musteri_id);
        $stmt->execute();
        $stmt->close();

        header("Location: satis.php");
        exit();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
