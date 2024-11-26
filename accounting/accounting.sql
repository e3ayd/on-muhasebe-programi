-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 26 Kas 2024, 11:45:07
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
(1, 'iş tamamlandı', 60000.00, '2024-11-25', '2024-11-25 20:57:33'),
(2, 'iş tamamlandı', 40000.00, '2024-11-25', '2024-11-25 20:57:45');

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
(1, 'iş tamamlandı', 20000.00, '2024-11-25', '2024-11-25 20:56:16'),
(2, 'maas ödemesi 2', 30000.00, '2024-11-25', '2024-11-25 20:57:06');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `calisan_odeme`
--
ALTER TABLE `calisan_odeme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `faturalar`
--
ALTER TABLE `faturalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `gelirler`
--
ALTER TABLE `gelirler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `satis`
--
ALTER TABLE `satis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `calisan_odeme`
--
ALTER TABLE `calisan_odeme`
  ADD CONSTRAINT `calisan_odeme_ibfk_1` FOREIGN KEY (`calisan_id`) REFERENCES `calisanlar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
