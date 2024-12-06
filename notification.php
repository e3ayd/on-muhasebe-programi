<?php
session_start(); // Oturum başlatılması gerekir

// Bildirim var mı kontrol et
if (isset($_SESSION['notification'])) {
    // Bildirimi bir div içinde göster
    echo '<div class="notification ' . htmlspecialchars($_SESSION['notification']['type']) . '">';
    echo '<p>' . htmlspecialchars($_SESSION['notification']['message']) . '</p>';
    echo '</div>';
    // Bildirim gösterildikten sonra oturumdan sil
    unset($_SESSION['notification']);
}
?>

<style>
/* Bildirimler için genel stil */
.notification {
    position: fixed; /* Sabit pozisyon */
    top: 20px; /* Sayfanın üstünden 20px */
    right: 20px; /* Sayfanın sağından 20px */
    background-color: #fefefe; /* Açık renkli arka plan */
    border-left: 5px solid; /* Sol tarafta kategoriye göre renk */
    padding: 15px 20px; /* İçerik boşluğu */
    border-radius: 8px; /* Yuvarlatılmış köşeler */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Hafif gölge efekti */
    font-family: Arial, sans-serif; /* Yazı tipi */
    color: #333; /* Yazı rengi */
    animation: slideIn 0.5s ease; /* Kayarak giriş animasyonu */
    z-index: 1000; /* Üstte görünmesi için */
    min-width: 250px; /* Minimum genişlik */
}

/* Başarı bildirimi */
.notification.success {
    border-color: #4caf50; /* Yeşil renk */
}

/* Hata bildirimi */
.notification.error {
    border-color: #f44336; /* Kırmızı renk */
}

/* Bilgi bildirimi */
.notification.info {
    border-color: #2196f3; /* Mavi renk */
}

/* Uyarı bildirimi */
.notification.warning {
    border-color: #ff9800; /* Turuncu renk */
}

/* Animasyon: Kayarak giriş */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Bildirimi kapat butonu */
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
</style>

