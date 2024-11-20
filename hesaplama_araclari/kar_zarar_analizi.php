<?php
require_once '../header.php'; // Header Include
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kar/Zarar Analizi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="content">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Kar/Zarar Analizi</h2>
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="gelir" class="form-label">Toplam Gelir</label>
                    <input type="number" class="form-control" id="gelir" name="gelir" placeholder="Gelirinizi giriniz" required>
                </div>
                <div class="mb-3">
                    <label for="gider" class="form-label">Toplam Gider</label>
                    <input type="number" class="form-control" id="gider" name="gider" placeholder="Giderinizi giriniz" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Hesapla</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $gelir = $_POST['gelir'];
                $gider = $_POST['gider'];

                $sonuc = $gelir - $gider;
                $mesaj = $sonuc >= 0 ? "Karınız: " : "Zararınız: ";
                $sinif = $sonuc >= 0 ? "alert-success" : "alert-danger";

                echo "<div class='alert $sinif'>
                    <strong>$mesaj</strong> " . number_format(abs($sonuc), 2) . " ₺
                </div>";
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
