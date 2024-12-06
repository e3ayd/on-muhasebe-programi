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

// Fatura silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_id'])) {
    $sil_id = intval($_POST['sil_id']); // Güvenlik için intval kullanımı

    $stmt = $conn->prepare("DELETE FROM faturalar WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $sil_id);
        if ($stmt->execute()) {
            $conn->query("SET @new_id = 0;");
            $conn->query("UPDATE faturalar SET id = (@new_id := @new_id + 1);");
            $conn->query("ALTER TABLE faturalar AUTO_INCREMENT = 1;");

            $_SESSION['notification'] = [
                'type' => 'danger',
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
                                <th>Tarih</th>
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
                                        <td>{$row['created_at']}</td>
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
                                echo "<tr><td colspan='8' class='text-center'>Kayıt bulunamadı.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchTable() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#faturaListesi tbody tr");

            rows.forEach(row => {
                const cells = row.getElementsByTagName("td");
                let found = false;

                for (let i = 1; i < cells.length; i++) {
                    if (cells[i].innerText.toLowerCase().includes(input)) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? "" : "none";
            });
        }

        function searchByDate() {
            const dateInput = document.getElementById("calendarInput").value;
            const rows = document.querySelectorAll("#faturaListesi tbody tr");

            rows.forEach(row => {
                const dateCell = row.getElementsByTagName("td")[6].innerText;
                row.style.display = dateCell.includes(dateInput) ? "" : "none";
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
