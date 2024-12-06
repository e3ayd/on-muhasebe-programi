<?php
require_once 'header.php'; // Header ve veritabanı bağlantısı


// Bildirim gösterimi
if (isset($_SESSION['notification'])) {
    $type = htmlspecialchars($_SESSION['notification']['type']);
    $message = htmlspecialchars($_SESSION['notification']['message']);
    echo "
    <div class='notification $type'>
        <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
        <p>$message</p>
    </div>";
    unset($_SESSION['notification']); // Bildirimi gösterdikten sonra temizle
}

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

// Yapılacaklar ve tamamlananlar verilerini çek
$sql_yapilacaklar = "SELECT * FROM notlar WHERE durum = 'yapilacak'";
$result_yapilacaklar = $conn->query($sql_yapilacaklar);

$sql_tamamlananlar = "SELECT * FROM notlar WHERE durum = 'tamamlandi'";
$result_tamamlananlar = $conn->query($sql_tamamlananlar);

// Yeni görev ekleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_task'])) {
    $baslik = $_POST['task_title'];
    $aciklama = $_POST['task_description'];
    $sql_insert = "INSERT INTO notlar (baslik, aciklama, durum, olusturulma_tarihi) VALUES ('$baslik', '$aciklama', 'yapilacak', NOW())";
    if ($conn->query($sql_insert) === TRUE) {
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Yeni not başarıyla eklendi!'
        ];
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Not ekleme sırasında bir hata oluştu!'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Görev tamamlandı olarak işaretleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete_task_id'])) {
    $task_id = $_POST['complete_task_id'];
    $sql_update = "UPDATE notlar SET durum = 'tamamlandi', tamamlanma_tarihi = NOW() WHERE id = $task_id";
    if ($conn->query($sql_update) === TRUE) {
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Not başarıyla tamamlandı!'
        ];
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Not tamamlama sırasında bir hata oluştu!'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Görev silme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_task_id'])) {
    $task_id = $_POST['delete_task_id'];
    $sql_delete = "DELETE FROM notlar WHERE id = $task_id";
    if ($conn->query($sql_delete) === TRUE) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Not başarıyla silindi!'
        ];
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'Not silinirken bir hata oluştu!'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notlarım</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .task-container {
            display: flex;
            gap: 20px;
        }
        .card {
            flex: 1;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #add-task-form {
            margin-top: 15px;
        }
        #add-task-form input[type="text"],
        #add-task-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #add-task-form button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            margin-bottom: 15px;
            cursor: pointer;
        }
        #add-task-form button:hover {
            background-color: #0056b3;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            padding: 8px 12px;
            margin-bottom: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
        }
        ul li form {
            display: inline;
        }
        ul li button {
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        ul li button:hover {
            background-color: #218838 ;
        }
        .custom-btn {
        background-color: #ccc; /* Normal durum arka plan rengi */
        color: black; /* Normal durum yazı rengi */
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s; /* Geçiş efekti */
        }

        .custom-btn:hover {
        background-color: white; /* Hover durumu arka plan rengi */
        color: black; /* Hover durumu yazı rengi */
        }

        .custom-btn:active {
        background-color: #ccc; /* Tıklama sırasında arka plan rengi (aynı kalır) */
        color: black; /* Tıklama sırasında yazı rengi */
        box-shadow: none; /* Bootstrap'ten gelen gölgeleri kaldırır */
        outline: none; /* Focus durumunda kenarlık görünmez */
        }
        .notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #fefefe;
    border-left: 5px solid;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
    color: #333;
    z-index: 1000;
    min-width: 300px;
    animation: slideIn 0.4s ease;
}
.notification.success {
    border-color: #4caf50;
    background-color: #e8f5e9;
    color: #2e7d32;
}
.notification.danger {
    border-color: #f44336;
    background-color: #ffebee;
    color: #c62828;
}
.notification .close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 18px;
    color: #888;
    cursor: pointer;
    transition: color 0.3s ease;
}
.notification .close-btn:hover {
    color: #000;
}
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
            <h2 class="mt-4 mb-3">Notlarım</h2>
            <div class="task-container">
                <!-- Yapılacaklar Kartı -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <span>Yapılacaklar</span>
                        <button id="add-task-btn" class="custom-btn" onclick="toggleAddTaskForm()">+</button>
                        </div>
                    <div class="card-body">
                        <div id="add-task-form" style="display:none;">
                            <form method="POST">
                                <input type="text" name="task_title" placeholder="Görev Başlığı" required>
                                <textarea name="task_description" placeholder="Görev Açıklaması" required></textarea>
                                <button type="submit" name="new_task">Ekle</button>
                            </form>
                        </div>
                        <ul id="task-list">
                            <?php
                            if ($result_yapilacaklar->num_rows > 0) {
                                while ($row = $result_yapilacaklar->fetch_assoc()) {
                                    echo "<li style='display: flex; justify-content: space-between; align-items: center;'>
                                        <div>
                                            <strong>" . $row["baslik"] . "</strong><br>
                                            <small>" . $row["aciklama"] . "</small><br>
                                            <small>Oluşturulma Tarihi: " . $row["olusturulma_tarihi"] . "</small>
                                        </div>
                                        <form method='POST'>
                                            <button type='submit' name='complete_task_id' value='" . $row["id"] . "'>Tamamla</button>
                                        </form>
                                    </li>";
                                }
                            } else {
                                echo "<li>Henüz yapılacak bir iş yok.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <!-- Tamamlananlar Kartı -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <span>Tamamlananlar</span>
                    </div>
                    <div class="card-body">
                        <ul id="completed-task-list">
                            <?php
                            if ($result_tamamlananlar->num_rows > 0) {
                                while ($row = $result_tamamlananlar->fetch_assoc()) {
                                    echo "<li style='display: flex; justify-content: space-between; align-items: center;'>
                                        <div>
                                            <strong>" . $row["baslik"] . "</strong><br>
                                            <small>" . $row["aciklama"] . "</small><br>
                                            <small>Tamamlanma Tarihi: " . $row["tamamlanma_tarihi"] . "</small>
                                        </div>
                                        <form method='POST' style='margin-left: 10px;'>
                                            <button type='submit' name='delete_task_id' value='" . $row["id"] . "' style='background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;'>Sil</button>
                                        </form>
                                    </li>";
                                }
                            } else {
                                echo "<li>Henüz tamamlanan bir iş yok.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleAddTaskForm() {
            const form = document.getElementById('add-task-form');
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
