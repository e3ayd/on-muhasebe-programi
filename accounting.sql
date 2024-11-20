-- Tablo için tablo yapısı calisanlar
--

CREATE TABLE calisanlar (
  id int(11) NOT NULL,
  ad varchar(255) NOT NULL,
  pozisyon varchar(255) NOT NULL,
  maas decimal(10,2) NOT NULL,
  avans decimal(10,2) DEFAULT 0.00,
  ekstra_odeme1 decimal(10,2) DEFAULT 0.00,
  ekstra_odeme2 decimal(10,2) DEFAULT 0.00,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi calisanlar
--

INSERT INTO calisanlar (id, ad, pozisyon, maas, avans, ekstra_odeme1, ekstra_odeme2, created_at) VALUES
-- --------------------------------------------------------

--
-- Tablo için tablo yapısı calisan_odeme
--

CREATE TABLE calisan_odeme (
  id int(11) NOT NULL,
  calisan_id int(11) NOT NULL,
  miktar decimal(10,2) NOT NULL,
  tarih date NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı faturalar
--

CREATE TABLE faturalar (
  id int(11) NOT NULL,
  musteri_adi varchar(255) NOT NULL,
  fatura_no varchar(100) NOT NULL,
  tutar decimal(10,2) NOT NULL,
  odenen decimal(10,2) DEFAULT 0.00,
  tarih date NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı gelirler
--

CREATE TABLE gelirler (
  id int(11) NOT NULL,
  aciklama varchar(255) NOT NULL,
  miktar decimal(10,2) NOT NULL,
  tarih date NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı giderler
--

CREATE TABLE giderler (
  id int(11) NOT NULL,
  aciklama varchar(255) NOT NULL,
  miktar decimal(10,2) NOT NULL,
  tarih date NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı kullanicilar
--

CREATE TABLE kullanicilar (
  id int(11) NOT NULL,
  email varchar(255) NOT NULL,
  sifre varchar(255) NOT NULL,
  izin varchar(50) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi kullanicilar
--

INSERT INTO kullanicilar (id, email, sifre, izin, created_at) VALUES

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler calisanlar
--
ALTER TABLE calisanlar
  ADD PRIMARY KEY (id);

--
-- Tablo için indeksler calisan_odeme
--
ALTER TABLE calisan_odeme
  ADD PRIMARY KEY (id),
  ADD KEY calisan_id (calisan_id),
  ADD KEY idx_calisan_odeme_tarih (tarih);

--
-- Tablo için indeksler faturalar
--
ALTER TABLE faturalar
  ADD PRIMARY KEY (id),
  ADD KEY idx_faturalar_tarih (tarih);

--
-- Tablo için indeksler gelirler
--
ALTER TABLE gelirler
  ADD PRIMARY KEY (id),
  ADD KEY idx_gelirler_tarih (tarih);

--
-- Tablo için indeksler giderler
--
ALTER TABLE giderler
  ADD PRIMARY KEY (id),
  ADD KEY idx_giderler_tarih (tarih);

--
-- Tablo için indeksler kullanicilar
--
ALTER TABLE kullanicilar
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri calisanlar
--
ALTER TABLE calisanlar
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri calisan_odeme
--
ALTER TABLE calisan_odeme
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri faturalar
--
ALTER TABLE faturalar
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri gelirler
--
ALTER TABLE gelirler
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri giderler
--
ALTER TABLE giderler
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri kullanicilar
--
ALTER TABLE kullanicilar
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları calisan_odeme
--
ALTER TABLE calisan_odeme
  ADD CONSTRAINT calisan_odeme_ibfk_1 FOREIGN KEY (calisan_id) REFERENCES calisanlar (id) ON DELETE CASCADE;
COMMIT;
