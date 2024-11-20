<?php
require_once '../header.php'; // Header Include
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Döviz Çevirici</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="content">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Döviz Çevirici</h2>
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="miktar" class="form-label">Miktar</label>
                    <input type="number" class="form-control" id="miktar" name="miktar" placeholder="Miktarı giriniz" required>
                </div>
                <div class="mb-3">
                    <label for="kur" class="form-label">Döviz Kuru</label>
                    <input type="number" step="0.01" class="form-control" id="kur" name="kur" placeholder="Döviz kurunu giriniz" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Hesapla</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $miktar = $_POST['miktar'];
                $kur = $_POST['kur'];

                $sonuc = $miktar * $kur;

                echo "<div class='alert alert-success'>
                    <strong>Toplam Tutar:</strong> " . number_format($sonuc, 2) . " ₺
                </div>";
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
