<?php
require_once 'header.php'; // Header ve kullanıcı doğrulama
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
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM faturalar ORDER BY tarih DESC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $kalanBorc = $row['tutar'] - $row['odenen'];
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['musteri_adi']}</td>
                                        <td>{$row['fatura_no']}</td>
                                        <td>{$row['tutar']} ₺</td>
                                        <td>{$row['odenen']} ₺</td>
                                        <td class='" . ($kalanBorc > 0 ? "borc" : "") . "'>" . ($kalanBorc > 0 ? $kalanBorc . " ₺" : "Yok") . "</td>
                                        <td>
                                            <button class='btn btn-sm btn-warning' onclick='acDuzeltModal({$row['id']})'>Düzenle</button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Kayıt bulunamadı.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Düzenleme Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="editModalLabel">Fatura Ödemesini Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        <input type="hidden" name="fatura_id" id="editFaturaId">
                        <div class="mb-3">
                            <label for="editFaturaNo" class="form-label">Fatura No</label>
                            <input type="text" class="form-control" id="editFaturaNo" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editOdenenTutar" class="form-label">Ödenen Tutar</label>
                            <input type="number" class="form-control" name="odenen" id="editOdenenTutar" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function acDuzeltModal(faturaId) {
            fetch(`get_fatura.php?id=${faturaId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editFaturaId').value = data.id;
                    document.getElementById('editFaturaNo').value = data.fatura_no;
                    document.getElementById('editOdenenTutar').value = data.odenen;
                    const modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();
                });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
