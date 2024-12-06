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
    unset($_SESSION['notification']);
}

// Gider Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aciklama'], $_POST['miktar'], $_POST['tarih'])) {
    $aciklama = $_POST['aciklama'];
    $miktar = $_POST['miktar'];
    $tarih = $_POST['tarih'];

    $stmt = $conn->prepare("INSERT INTO giderler (aciklama, miktar, tarih) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sds", $aciklama, $miktar, $tarih);
        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Gider başarıyla eklendi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Gider eklenirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Gider ekleme işlemi başlatılamadı!'
        ];
    }
    header("Location: giderler.php");
    exit();
}

// Gider Silme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = intval($_POST['sil_id']);

    $stmt = $conn->prepare("DELETE FROM giderler WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $sil_id);
        if ($stmt->execute()) {
            $conn->query("SET @new_id = 0;");
            $conn->query("UPDATE giderler SET id = (@new_id := @new_id + 1) ORDER BY id ASC;");
            $conn->query("ALTER TABLE giderler AUTO_INCREMENT = 1;");

            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Gider başarıyla silindi!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Gider silinirken bir hata oluştu!'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Gider silme işlemi başlatılamadı!'
        ];
    }
    header("Location: giderler.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giderlerim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h2 class="mt-4 mb-3">Giderlerim</h2>

            <!-- Aylık Toplam Gider -->
            <div class="alert alert-warning">
                <strong>Bu ay toplam gider:</strong> 
                <span id="aylikToplamGider">
                    <?php
                    $currentMonth = date('Y-m');
                    $query = "SELECT SUM(miktar) AS toplam FROM giderler WHERE DATE_FORMAT(tarih, '%Y-%m') = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $currentMonth);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    echo $row['toplam'] ?? 0;
                    ?>
                </span> ₺
            </div>

            <!-- Filtreler -->
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
                <input type="text" id="searchInput" class="form-control" placeholder="Gider ara..." onkeyup="searchGider()">
            </div>

            <!-- Gider Ekleme Formu -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Yeni Gider Ekle</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="giderAciklama" class="form-label">Açıklama</label>
                            <input type="text" class="form-control" name="aciklama" placeholder="Gider Açıklaması" required>
                        </div>
                        <div class="mb-3">
                            <label for="giderMiktari" class="form-label">Miktar</label>
                            <input type="number" class="form-control" name="miktar" placeholder="Gider Miktarı" required>
                        </div>
                        <div class="mb-3">
                            <label for="giderTarihi" class="form-label">Tarih</label>
                            <input type="date" class="form-control" name="tarih" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </form>
                </div>
            </div>

            <!-- Gider Listesi -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Gider Listesi</div>
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
                            $query = "SELECT * FROM giderler WHERE DATE_FORMAT(tarih, '%Y-%m') = ? ORDER BY id ASC"; // ID'ye göre sıralama
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
                                            <a href='gider_duzenle.php?id={$row['id']}' class='btn btn-sm btn-primary'>Düzenle</a>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu gideri silmek istediğinize emin misiniz?\");'>Sil</button>
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
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark text-white">Aylık Gider Grafiği</div>
                        <div class="card-body">
                            <canvas id="aylikGiderGrafik"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark text-white">Gider Dağılımı</div>
                        <div class="card-body">
                            <canvas id="giderDagilimGrafik"></canvas>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</body>
</html>
