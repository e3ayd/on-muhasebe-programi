<?php
require_once 'header.php'; // Veritabanı bağlantısını içerdiğinden emin olun

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Müşteriyi veritabanından silme işlemi
    $stmt = $conn->prepare("DELETE FROM musteriler WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Kalan müşteri ID'lerini yeniden sıralama
        $conn->query("SET @new_id = 0;"); // Yeni ID sıfırlama değişkeni
        $conn->query("UPDATE musteriler SET id = (@new_id := @new_id + 1);"); // ID'leri sırayla güncelle
        $conn->query("ALTER TABLE musteriler AUTO_INCREMENT = 1;"); // AUTO_INCREMENT değerini sıfırla

        // Silme işleminden sonra listeye yönlendirme
        header("Location: musteri.php");
        exit();
    } else {
        die("Müşteri silme hatası: " . $conn->error);
    }
} else {
    die("Geçersiz müşteri ID'si.");
}
?>
