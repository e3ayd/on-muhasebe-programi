-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 09 Ara 2024, 12:55:21
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `accounting`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `calisanlar`
--

CREATE TABLE `calisanlar` (
  `id` int(11) NOT NULL,
  `ad` varchar(255) NOT NULL,
  `pozisyon` varchar(255) NOT NULL,
  `maas` decimal(10,2) NOT NULL,
  `avans` decimal(10,2) DEFAULT 0.00,
  `ekstra_odeme1` decimal(10,2) DEFAULT 0.00,
  `ekstra_odeme2` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `calisanlar`
--

INSERT INTO `calisanlar` (`id`, `ad`, `pozisyon`, `maas`, `avans`, `ekstra_odeme1`, `ekstra_odeme2`, `created_at`) VALUES
(1, 'ozi', 'çalışan', 123.00, 12.00, 123.00, 123.00, '2024-11-27 09:32:39'),
(2, 'ozi', 'çalışan', 1234.00, 1234.00, 1234.00, 1234.00, '2024-12-03 12:02:14'),
(3, 'ozi', 'calisan2', 1234.00, 1234.00, 1234.00, 1234.00, '2024-12-03 13:06:20');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `calisan_odeme`
--

CREATE TABLE `calisan_odeme` (
  `id` int(11) NOT NULL,
  `calisan_id` int(11) NOT NULL,
  `miktar` decimal(10,2) NOT NULL,
  `tarih` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `calisan_odeme`
--

INSERT INTO `calisan_odeme` (`id`, `calisan_id`, `miktar`, `tarih`, `created_at`) VALUES
(1, 2, 1234.00, '2024-12-03', '2024-12-03 12:02:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `faturalar`
--

CREATE TABLE `faturalar` (
  `id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `musteri_adi` varchar(255) NOT NULL,
  `fatura_no` varchar(100) NOT NULL,
  `miktar` decimal(10,2) NOT NULL,
  `odenen` decimal(10,2) DEFAULT 0.00,
  `dosya` varchar(255) NOT NULL,
  `tarih` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `faturalar`
--

INSERT INTO `faturalar` (`id`, `baslik`, `musteri_adi`, `fatura_no`, `miktar`, `odenen`, `dosya`, `tarih`, `created_at`) VALUES
(1, '1234', 'Oğuzhan Aktuğ', '1234', 1234.00, 1234.00, 'uploads/deneme.txt.txt', '0000-00-00', '2024-12-03 11:34:28'),
(2, '1234', 'Oğuzhan Aktuğ', '1234', 1234.00, 1234.00, 'uploads/deneme.txt.txt', '0000-00-00', '2024-12-03 11:42:31'),
(3, '1234', 'Oğuzhan Aktuğ', '1234', 1234.00, 1234.00, 'uploads/deneme.txt.txt', '0000-00-00', '2024-12-03 12:02:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gelirler`
--

CREATE TABLE `gelirler` (
  `id` int(11) NOT NULL,
  `aciklama` varchar(255) NOT NULL,
  `miktar` decimal(10,2) NOT NULL,
  `tarih` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `gelirler`
--

INSERT INTO `gelirler` (`id`, `aciklama`, `miktar`, `tarih`, `created_at`) VALUES
(1, '1234', 1234.00, '2024-12-03', '2024-12-03 14:00:54'),
(2, 'iş tamamlandı', 12322.00, '2024-12-03', '2024-12-03 14:07:43'),
(3, '1234', 1234.00, '2024-12-04', '2024-12-04 14:05:45');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `giderler`
--

CREATE TABLE `giderler` (
  `id` int(11) NOT NULL,
  `aciklama` varchar(255) NOT NULL,
  `miktar` decimal(10,2) NOT NULL,
  `tarih` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `giderler`
--

INSERT INTO `giderler` (`id`, `aciklama`, `miktar`, `tarih`, `created_at`) VALUES
(1, '1234', 1234.00, '2024-12-03', '2024-12-03 14:07:08'),
(2, '1234', 1234.00, '2024-12-03', '2024-12-03 14:07:22');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `izin` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `email`, `sifre`, `izin`, `created_at`) VALUES
(1, 'emirhan@volaresoft.com', '$2y$10$5oF/FNIlqW2AArl1HGTsYO/ztKvCRoJkb4ksImKwOeFhafuZFK3iK', 'admin', '2024-11-19 23:15:53'),
(2, 'oguzhan@volaresoft.com', '$2y$10$DJt1U6w5/fJjBR7nX51dDOwjzsOeyeaJeVIwlctBTXgCSMzcdrmRC', 'admin', '2024-11-23 19:21:52');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `musteriler`
--

CREATE TABLE `musteriler` (
  `id` int(11) NOT NULL,
  `ad_soyad` varchar(255) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `firma_adi` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `musteriler`
--

INSERT INTO `musteriler` (`id`, `ad_soyad`, `telefon`, `firma_adi`, `email`) VALUES
(1, 'Oğuzhan Aktuğ', '0555 555 55 55', 'Volaresoft', 'oguzhan@volaresoft.com'),
(2, 'Oğuzhan Aktuğ', '0555 555 55 55', 'Volaresoft123', 'ozi@gmail.com12'),
(3, 'Oğuzhan Aktuğ', '0555 555 55 55', 'Volaresoft123', 'ozi@gmail.com12'),
(4, '1234', '1234', '1234', '1234@1234.com'),
(5, '1234', '1234', '1234', '1234@1234.com'),
(6, 'Oğuzhan Aktuğ', '0555 555 55 56', 'Volaresoft', 'oguzhan@volaresoft.com'),
(7, 'Oğuzhan Aktuğ', '0555 555 55 55', 'Volaresoft', 'oguzhan@volaresoft.com'),
(8, 'Oğuzhan Aktuğ', '123', '123', '1234@1234.com'),
(9, 'Oğuzhan Aktuğ', '1234', '1234', '1234@1234.com');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `due_date` date DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `due_date`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'Maaş Ödeme Hatırlatması', 'Maaş ödemeniz 5 gün içinde yapılacaktır.', '2024-12-01', 0, '2024-11-27 08:57:02', '2024-11-27 08:57:02'),
(2, 2, 'Fatura Ödemesi Yaklaşıyor', 'Elektrik faturanızın son ödeme tarihi yaklaşıyor.', '2024-12-05', 0, '2024-11-27 08:57:02', '2024-11-27 08:57:02');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notlar`
--

CREATE TABLE `notlar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `durum` enum('yapilacak','yapildi','tamamlandi') NOT NULL,
  `olusturulma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `tamamlanma_tarihi` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notlar`
--

INSERT INTO `notlar` (`id`, `user_id`, `baslik`, `aciklama`, `durum`, `olusturulma_tarihi`, `tamamlanma_tarihi`) VALUES
(125, 0, '1234', '1234', 'yapilacak', '2024-12-02 16:16:41', NULL),
(126, 0, '1234', '1234', 'yapilacak', '2024-12-02 16:17:11', NULL),
(127, 0, '1234', '1234', 'yapilacak', '2024-12-02 16:17:13', NULL),
(128, 0, '1234', '1234', 'yapilacak', '2024-12-02 16:17:15', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `notification_status` enum('pending','notified') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_reminder_sent` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `satis`
--

CREATE TABLE `satis` (
  `id` int(11) NOT NULL,
  `musteri_id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `urun_adi` varchar(255) NOT NULL,
  `birim_fiyati` decimal(10,2) NOT NULL,
  `miktar` int(11) NOT NULL,
  `fatura_no` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `satis`
--

INSERT INTO `satis` (`id`, `musteri_id`, `baslik`, `urun_adi`, `birim_fiyati`, `miktar`, `fatura_no`, `created_at`) VALUES
(1, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-03 13:21:02'),
(2, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-03 13:23:13'),
(3, 1, '2134', '1234', 1234.00, 1234, '1234', '2024-12-03 13:23:43'),
(4, 1, '2134', '1234', 1234.00, 1234, '1234', '2024-12-03 13:24:11'),
(5, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-03 13:24:15'),
(6, 2, '1234', '123', 1234.00, 1234, '1234', '2024-12-03 13:26:49'),
(7, 2, '1234', '123', 1234.00, 1234, '1234', '2024-12-03 13:28:43'),
(8, 1, '1234', '123', 123.00, 2123, '123', '2024-12-04 13:27:50'),
(9, 1, '1234', '123', 1234.00, 1234, '1234', '2024-12-04 13:29:21'),
(10, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-04 13:30:03'),
(11, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-04 13:30:46'),
(12, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-04 13:34:16'),
(13, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-04 13:42:59'),
(14, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-04 13:43:03'),
(15, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-04 13:52:41'),
(16, 1, '1234', '1234', 1234.00, 1234, '1234', '2024-12-04 14:05:02'),
(17, 3, '1234', '1234', 1234.00, 1234, '1234', '2024-12-06 11:20:28');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `calisanlar`
--
ALTER TABLE `calisanlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `calisan_odeme`
--
ALTER TABLE `calisan_odeme`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_calisan_odeme_tarih` (`tarih`),
  ADD KEY `calisan_odeme_ibfk_1` (`calisan_id`);

--
-- Tablo için indeksler `faturalar`
--
ALTER TABLE `faturalar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faturalar_tarih` (`tarih`);

--
-- Tablo için indeksler `gelirler`
--
ALTER TABLE `gelirler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gelirler_tarih` (`tarih`);

--
-- Tablo için indeksler `giderler`
--
ALTER TABLE `giderler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_giderler_tarih` (`tarih`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `musteriler`
--
ALTER TABLE `musteriler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `notlar`
--
ALTER TABLE `notlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `satis`
--
ALTER TABLE `satis`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `calisanlar`
--
ALTER TABLE `calisanlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `calisan_odeme`
--
ALTER TABLE `calisan_odeme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `faturalar`
--
ALTER TABLE `faturalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `gelirler`
--
ALTER TABLE `gelirler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `giderler`
--
ALTER TABLE `giderler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `musteriler`
--
ALTER TABLE `musteriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `notlar`
--
ALTER TABLE `notlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- Tablo için AUTO_INCREMENT değeri `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `satis`
--
ALTER TABLE `satis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `calisan_odeme`
--
ALTER TABLE `calisan_odeme`
  ADD CONSTRAINT `calisan_odeme_ibfk_1` FOREIGN KEY (`calisan_id`) REFERENCES `calisanlar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `kullanicilar` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
