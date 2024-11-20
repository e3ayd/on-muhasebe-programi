<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama
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
                            <label for="faturaMiktari" class="form-label">Fatura Miktarı</label>
                            <input type="number" class="form-control" name="miktar" placeholder="Fatura Miktarı" required>
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
                                <th>Miktar</th>
                                <th>Dosya</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM faturalar ORDER BY created_at DESC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['baslik']}</td>
                                        <td>{$row['miktar']} ₺</td>
                                        <td><a href='{$row['dosya']}' target='_blank'>Dosyayı Görüntüle</a></td>
                                        <td>{$row['created_at']}</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Fatura bulunamadı.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Fatura Ekleme İşlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['baslik'], $_POST['miktar'])) {
        $baslik = $_POST['baslik'];
        $miktar = $_POST['miktar'];

        // Dosya Yükleme
        $dosyaAdi = basename($_FILES['fatura_dosyasi']['name']);
        $dosyaYolu = "uploads/" . $dosyaAdi;
        move_uploaded_file($_FILES['fatura_dosyasi']['tmp_name'], $dosyaYolu);

        $stmt = $conn->prepare("INSERT INTO faturalar (baslik, miktar, dosya) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $baslik, $miktar, $dosyaYolu);
        $stmt->execute();
        $stmt->close();

        header("Location: faturalar.php");
        exit();
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
