<?php
require_once 'header.php'; // Header ve veritabanı bağlantısı

// Veritabanından yapılacaklar ve tamamlananlar notlarını çek
$yapilacaklar = $db->query("SELECT * FROM notlar WHERE durum = 'yapılacak'")->fetchAll(PDO::FETCH_ASSOC);
$tamamlananlar = $db->query("SELECT * FROM notlar WHERE durum = 'tamamlandı'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yapılacaklar ve Tamamlananlar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .card-header {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="container">

            <!-- Yapılacaklar Listesi -->
            <h2 class="mt-4 mb-3">Yapılacaklar</h2>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Yapılacaklar
                </div>
                <div class="card-body">
                    <?php if (!empty($yapilacaklar)): ?>
                        <ul class="list-group">
                            <?php foreach ($yapilacaklar as $yap): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($yap['baslik']); ?></span>
                                    <button class="btn btn-success btn-sm" onclick="tamamla(<?= $yap['id']; ?>)">Tamamla</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Henüz yapılacak bir iş yok.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tamamlananlar Listesi -->
            <h2 class="mt-4 mb-3">Tamamlananlar</h2>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    Tamamlananlar
                </div>
                <div class="card-body">
                    <?php if (!empty($tamamlananlar)): ?>
                        <ul class="list-group">
                            <?php foreach ($tamamlananlar as $tam): ?>
                                <li class="list-group-item">
                                    <?= htmlspecialchars($tam['baslik']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Henüz tamamlanan bir iş yok.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function tamamla(id) {
            if (confirm("Bu işi tamamladığınıza emin misiniz?")) {
                fetch(`tamamla.php?id=${id}`)
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Hata:', error));
            }
        }
    </script>
</body>
</html>
