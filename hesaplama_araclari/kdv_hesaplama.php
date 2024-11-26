<?php
require_once '../header.php'; // Header Include
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KDV Hesaplama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="content">
        <div class="container mt-5">
            <h2 class="text-center mb-4">KDV Hesaplama</h2>
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="tutar" class="form-label">Tutar</label>
                    <input type="number" class="form-control" id="tutar" name="tutar" placeholder="Tutarı giriniz" required>
                </div>
                <div class="mb-3">
                    <label for="kdvOrani" class="form-label">KDV Oranı (%)</label>
                    <select class="form-select" id="kdvOrani" name="kdvOrani" required>
                        <option value="20" selected>%20</option>
                        <option value="18" selected>%18</option>
                        <option value="10">%10</option>
                        <option value="8">%8</option>
                        <option value="1">%1</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Hesapla</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tutar = $_POST['tutar'];
                $kdvOrani = $_POST['kdvOrani'];

                $kdvTutari = $tutar * ($kdvOrani / 100);
                $toplamTutar = $tutar + $kdvTutari;

                echo "<div class='alert alert-success'>
                    <strong>KDV Tutarı:</strong> " . number_format($kdvTutari, 2) . " ₺<br>
                    <strong>Toplam Tutar:</strong> " . number_format($toplamTutar, 2) . " ₺
                </div>";
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
