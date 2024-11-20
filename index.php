<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .card-header {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
            <h2 class="mt-4 mb-3">Dashboard</h2>

            <!-- Genel Özet -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Toplam Gelir</div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php
                                $query = "SELECT SUM(miktar) AS toplamGelir FROM gelirler";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo number_format($row['toplamGelir'] ?? 0, 2) . " ₺";
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Toplam Gider</div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php
                                $query = "SELECT SUM(miktar) AS toplamGider FROM giderler";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo number_format($row['toplamGider'] ?? 0, 2) . " ₺";
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Bilanço</div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php
                                $queryGelir = "SELECT SUM(miktar) AS toplamGelir FROM gelirler";
                                $queryGider = "SELECT SUM(miktar) AS toplamGider FROM giderler";
                                $resultGelir = $conn->query($queryGelir)->fetch_assoc()['toplamGelir'] ?? 0;
                                $resultGider = $conn->query($queryGider)->fetch_assoc()['toplamGider'] ?? 0;
                                $balance = $resultGelir - $resultGider;
                                echo number_format($balance, 2) . " ₺";
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gelir ve Gider Grafikler -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">Aylık Gelir Grafiği</div>
                        <div class="card-body">
                            <canvas id="gelirGrafik"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">Aylık Gider Grafiği</div>
                        <div class="card-body">
                            <canvas id="giderGrafik"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Gelir ve Gider Verileri
    $aylikGelirler = [];
    $aylikGiderler = [];

    $queryGelir = "SELECT DATE_FORMAT(tarih, '%Y-%m') AS ay, SUM(miktar) AS toplamGelir FROM gelirler GROUP BY ay ORDER BY ay ASC";
    $queryGider = "SELECT DATE_FORMAT(tarih, '%Y-%m') AS ay, SUM(miktar) AS toplamGider FROM giderler GROUP BY ay ORDER BY ay ASC";

    $resultGelir = $conn->query($queryGelir);
    while ($row = $resultGelir->fetch_assoc()) {
        $aylikGelirler[$row['ay']] = $row['toplamGelir'];
    }

    $resultGider = $conn->query($queryGider);
    while ($row = $resultGider->fetch_assoc()) {
        $aylikGiderler[$row['ay']] = $row['toplamGider'];
    }

    $aylar = json_encode(array_keys($aylikGelirler + $aylikGiderler));
    $gelirVeri = json_encode(array_values($aylikGelirler));
    $giderVeri = json_encode(array_values($aylikGiderler));
    ?>

    <script>
        // Gelir Grafiği
        const gelirCtx = document.getElementById('gelirGrafik').getContext('2d');
        new Chart(gelirCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $aylar; ?>,
                datasets: [{
                    label: 'Gelir',
                    data: <?php echo $gelirVeri; ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Gider Grafiği
        const giderCtx = document.getElementById('giderGrafik').getContext('2d');
        new Chart(giderCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $aylar; ?>,
                datasets: [{
                    label: 'Gider',
                    data: <?php echo $giderVeri; ?>,
                    backgroundColor: 'rgba(192, 75, 75, 0.5)',
                    borderColor: 'rgba(192, 75, 75, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
