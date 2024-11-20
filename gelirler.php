<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gelirlerim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filters input, .filters select {
            width: 300px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
            <h2 class="mt-4 mb-3">Gelirlerim</h2>
            
            <!-- Aylık Toplam Gelir -->
            <div class="alert alert-info">
                <strong>Bu ay toplam gelir:</strong> 
                <span id="aylikToplamGelir">
                    <?php
                    $currentMonth = date('Y-m');
                    $query = "SELECT SUM(miktar) AS toplam FROM gelirler WHERE DATE_FORMAT(tarih, '%Y-%m') = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $currentMonth);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    echo $row['toplam'] ?? 0;
                    ?>
                </span> ₺
            </div>

            <div class="filters">
                <select id="aySecimi" class="form-select" onchange="location.href='?month=' + this.value;">
                    <?php
                    for ($i = 0; $i < 12; $i++) {
                        $month = date("Y-m", strtotime("-$i months"));
                        $selected = $month === $currentMonth ? "selected" : "";
                        echo "<option value='$month' $selected>$month</option>";
                    }
                    ?>
                </select>
                <input type="text" id="searchInput" class="form-control" placeholder="Gelir ara..." onkeyup="searchGelir()">
            </div>

            <!-- Gelir Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yeni Gelir Ekle
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="gelirAciklama" class="form-label">Açıklama</label>
                            <input type="text" class="form-control" name="aciklama" placeholder="Gelir Açıklaması" required>
                        </div>
                        <div class="mb-3">
                            <label for="gelirMiktari" class="form-label">Miktar</label>
                            <input type="number" class="form-control" name="miktar" placeholder="Gelir Miktarı" required>
                        </div>
                        <div class="mb-3">
                            <label for="gelirTarihi" class="form-label">Tarih</label>
                            <input type="date" class="form-control" name="tarih" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </form>
                </div>
            </div>

            <!-- Gelir Listesi -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Gelir Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Açıklama</th>
                                <th>Miktar</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM gelirler WHERE DATE_FORMAT(tarih, '%Y-%m') = ? ORDER BY tarih DESC";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("s", $currentMonth);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['aciklama']}</td>
                                        <td>{$row['miktar']} ₺</td>
                                        <td>{$row['tarih']}</td>
                                        <td>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$row['id']}'>
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

            <!-- Gelir Grafikleri -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark text-white">Aylık Gelir Grafiği</div>
                        <div class="card-body">
                            <canvas id="aylikGelirGrafik"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark text-white">Gelir Dağılımı</div>
                        <div class="card-body">
                            <canvas id="gelirDagilimGrafik"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Gelir Ekleme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aciklama'], $_POST['miktar'], $_POST['tarih'])) {
        $aciklama = $_POST['aciklama'];
        $miktar = $_POST['miktar'];
        $tarih = $_POST['tarih'];

        $stmt = $conn->prepare("INSERT INTO gelirler (aciklama, miktar, tarih) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $aciklama, $miktar, $tarih);
        $stmt->execute();
        $stmt->close();

        header("Location: gelirler.php");
        exit();
    }

    // Gelir Silme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
        $sil_id = $_POST['sil_id'];

        $stmt = $conn->prepare("DELETE FROM gelirler WHERE id = ?");
        $stmt->bind_param("i", $sil_id);
        $stmt->execute();
        $stmt->close();

        header("Location: gelirler.php");
        exit();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
