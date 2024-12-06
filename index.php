<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama

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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .calendar {
        width: 100%;
        background: #fff;
        border-radius: 5px;
        overflow: hidden;
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: black;
        padding: 10px;
    }
    .calendar-header button {
        background: none;
        border: none;
        color: black;
        font-size: 16px;
        cursor: pointer;
    }
    .calendar-header h2 {
        margin: 0;
        font-size: 18px;
    }
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8f9fa;
        padding: 5px;
        font-weight: bold;
        font-size: 14px;
    }
    .calendar-days div {
        text-align: center;
    }
    .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    padding: 5px;
    height: 265px; /* Sabit yükseklik */
}
.calendar-grid div {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 40px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 14px;
}
    .calendar-grid div.today {
        background: #007bff;
        color: #fff;
    }
    .calendar-grid div:hover {
        transition: 0.5s;
        background: #007bff;
        color: #fff;
    }
    .calendar-grid div.empty {
        background: none;
        cursor: default;
    }
    .note {
    margin-bottom: 0px; 
    }

    .note h5 {
        margin-bottom: 0px;
    }

    .note p {
        margin-top: 0; 
        margin-bottom: 0px;
    }
    hr {
    border: 0; 
    border-top: 1px solid black;
    margin: 15.3px 0; 
    width: 100%; 
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

            <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">Notlarım</div>
                    <div class="card-body">
                        <?php
                        // Veritabanından veriyi çek
                        $sql = "SELECT baslik, aciklama FROM notlar WHERE durum = 'yapılacak' LIMIT 4";
                        $result = $conn->query($sql);

                        // Verileri mevcut div'in içine ekle
                        if ($result->num_rows > 0) {
                            $total_rows = $result->num_rows; // Toplam satır sayısını al
                            $current_row = 0; // Döngüdeki mevcut satırı takip et

                            while ($row = $result->fetch_assoc()) {
                                $current_row++; // Her döngüde mevcut satırı artır
                                echo '<div class="note">
                                        <h5>' . htmlspecialchars($row["baslik"]) . '</h5>
                                        <p>' . htmlspecialchars($row["aciklama"]) . '</p>
                                    </div>';
                                
                                // Son nota gelmediysek çizgi ekle
                                if ($current_row < $total_rows) {
                                    echo '<hr>';
                                }
                            }
                        } else {
                            echo "<p>Hiç not bulunamadı.</p>";
                        }
                        ?>
                        <div class="text-center mt-3">
                            <a href="notlarim.php" class="btn btn-primary">Tümünü Gör</a>
                        </div>
                    </div>
                </div>
            </div>


                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">Takvim</div>
                        <div class="card-body">
                            <div class="calendar">
                                <div class="calendar-header">
                                    <button id="prev">&lt;</button>
                                    <h2 id="month-year"></h2>
                                    <button id="next">&gt;</button>
                                </div>
                                <div class="calendar-days">
                                    <div>Pz</div>
                                    <div>Pt</div>
                                    <div>Sa</div>
                                    <div>Ça</div>
                                    <div>Pe</div>
                                    <div>Cu</div>
                                    <div>Ct</div>
                                </div>
                                <div class="calendar-grid" id="calendar-grid"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">Döviz</div>
                        <div class="card-body">
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
                                        $gosterilenSayisi = 6; // Görünmesini istediğiniz döviz sayısı
                                        $sayac = 0;
                                        foreach ($dovizKurlari as $paraBirimi => $kur) {
                                            if ($sayac >= $gosterilenSayisi) {
                                                break;
                                            }
                                            echo "<tr>
                                                <td>$paraBirimi</td>
                                                <td>" . number_format($kur, 4) . "</td>
                                            </tr>";
                                            $sayac++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='2'>Döviz bilgisi alınamadı.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="text-center mt-3">
                                <a href="hesaplama_araclari/canli_doviz.php" class="btn btn-primary">Tümünü Gör</a>
                            </div>
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


        //takvim:

        const monthNames = [
        "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran",
        "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"
    ];

    const calendarGrid = document.getElementById("calendar-grid");
    const monthYear = document.getElementById("month-year");
    const prevButton = document.getElementById("prev");
    const nextButton = document.getElementById("next");

    let currentDate = new Date();

    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        monthYear.textContent = `${monthNames[month]} ${year}`;
        calendarGrid.innerHTML = "";

        for (let i = 0; i < firstDayOfMonth; i++) {
            const emptyDiv = document.createElement("div");
            emptyDiv.classList.add("empty");
            calendarGrid.appendChild(emptyDiv);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayDiv = document.createElement("div");
            dayDiv.textContent = day;

            const today = new Date();
            if (
                day === today.getDate() &&
                month === today.getMonth() &&
                year === today.getFullYear()
            ) {
                dayDiv.classList.add("today");
            }

            calendarGrid.appendChild(dayDiv);
        }
    }

    prevButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    nextButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    renderCalendar(currentDate);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
