<?php
require_once '../header.php'; // Header Include

// API kullanarak döviz kurları çekilecek
$dovizKurlari = [];
try {
    $json = file_get_contents("https://api.exchangerate-api.com/v4/latest/USD");
    $data = json_decode($json, true);
    $dovizKurlari = $data['rates'];
} catch (Exception $e) {
    $dovizKurlari = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canlı Döviz Kurları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="content">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Canlı Döviz Kurları</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Döviz</th>
                        <th>Kur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($dovizKurlari)) {
                        foreach ($dovizKurlari as $paraBirimi => $kur) {
                            echo "<tr>
                                <td>$paraBirimi</td>
                                <td>" . number_format($kur, 4) . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>Döviz bilgisi alınamadı.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
