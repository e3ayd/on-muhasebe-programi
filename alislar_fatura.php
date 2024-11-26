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

    header("Location: alislar_fatura.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alışlar Fatura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .borc {
            color: red;
            font-weight: bold;
        }
        .search-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-bar input {
            width: 300px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
            <h2 class="mt-4 mb-3">Alışlar Fatura</h2>

            <!-- Search Bar and Calendar -->
            <div class="search-bar">
                <input type="text" id="searchInput" class="form-control" placeholder="Tarih veya müşteri adı ara..." onkeyup="searchTable()">
                <input type="date" id="calendarInput" class="form-control" onchange="searchByDate()">
            </div>

            <!-- Fatura Listesi -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Fatura Listesi
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover" id="faturaListesi">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Müşteri Adı</th>
                                <th>Fatura No</th>
                                <th>Fatura Tutarı</th>
                                <th>Ödenen Tutar</th>
                                <th>Kalan Borç</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM faturalar ORDER BY tarih DESC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $kalanBorc = $row['miktar'] - $row['odenen'];
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['musteri_adi']}</td>
                                        <td>{$row['fatura_no']}</td>
                                        <td>{$row['miktar']} ₺</td>
                                        <td>{$row['odenen']} ₺</td>
                                        <td class='" . ($kalanBorc > 0 ? "borc" : "") . "'>" . ($kalanBorc > 0 ? $kalanBorc . " ₺" : "Yok") . "</td>
                                       <td>
                                            <a href='alislar_fatura_duzenle.php?id={$row['id']}' class='btn btn-sm btn-primary'>Düzenle</a>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='sil_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Bu faturayı silmek istediğinize emin misiniz?\");'>Sil</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Kayıt bulunamadı.</td></tr>";
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
