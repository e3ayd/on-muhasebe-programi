<?php
require_once '../header.php'; // Header Include
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taksit Hesaplama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="content">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Taksit Hesaplama</h2>
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="tutar" class="form-label">Toplam Tutar</label>
                    <input type="number" class="form-control" id="tutar" name="tutar" placeholder="Tutarı giriniz" required>
                </div>
                <div class="mb-3">
                    <label for="vade" class="form-label">Vade (Ay)</label>
                    <input type="number" class="form-control" id="vade" name="vade" placeholder="Vade süresini giriniz" required>
                </div>
                <div class="mb-3">
                    <label for="faiz" class="form-label">Faiz Oranı (%)</label>
                    <input type="number" class="form-control" id="faiz" name="faiz" placeholder="Faiz oranını giriniz" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Hesapla</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tutar = $_POST['tutar'];
                $vade = $_POST['vade'];
                $faiz = $_POST['faiz'];

                $aylikFaizOrani = $faiz / 100 / 12;
                $aylikOdeme = $tutar * $aylikFaizOrani / (1 - pow(1 + $aylikFaizOrani, -$vade));
                $toplamOdeme = $aylikOdeme * $vade;

                echo "<div class='alert alert-success'>
                    <strong>Aylık Ödeme:</strong> " . number_format($aylikOdeme, 2) . " ₺<br>
                    <strong>Toplam Ödeme:</strong> " . number_format($toplamOdeme, 2) . " ₺
                </div>";
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
