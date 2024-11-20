# Ön Muhasebe Uygulaması

Bu proje, küçük ve orta ölçekli işletmeler için temel ön muhasebe işlemlerini kolaylaştırmak amacıyla geliştirilmiştir. Proje; gelir-gider yönetimi, fatura takibi, çalışan maaşları ve ödeme işlemleri gibi birçok işlemi desteklemektedir.

---

## Özellikler

1. **Hesaplama Araçları**:
   - Döviz kuru takibi ve döviz çevirici.
   - Kâr-zarar analizi.
   - KDV hesaplama.
   - Taksit hesaplama.

2. **Fatura Yönetimi**:
   - Alınan faturaları ekleme, görüntüleme ve düzenleme.
   - Kesilen faturaları ekleme, görüntüleme ve düzenleme.

3. **Çalışan Yönetimi**:
   - Çalışan ekleme ve maaş bilgisi kaydetme.
   - Çalışanlara ekstra ödeme ya da avans ekleme.
   - Çalışan ödeme geçmişini görüntüleme.

4. **Gelir-Gider Yönetimi**:
   - Gelir ve giderlerin eklenebileceği iki ayrı sayfa.
   - Grafik desteği ile gelir-gider durum analizi.

5. **Raporlama**:
   - Tüm faturaları görüntüleme ve kâr/zarar durumunu grafik ile analiz etme.

6. **Kullanıcı Yönetimi**:
   - Yönetici ve kullanıcı hesaplarının eklenmesi.
   - Giriş ve çıkış (Login/Logout) sistemi.

---

## Teknolojiler

- **Frontend**: HTML, Bootstrap, CSS, JavaScript
- **Backend**: PHP
- **Veritabanı**: MySQL

---

## Kurulum

### Gereksinimler

- PHP (v8.1 veya üzeri)
- MySQL (v10.6 veya üzeri)
- Apache Web Server veya benzeri bir sunucu
- phpMyAdmin (opsiyonel)

### Kurulum Adımları

1. **Proje Dosyalarını İndirin**:
   ```bash
   git clone https://github.com/e3ayd/on-muhasebe-programi.git
   cd on-muhasebe-programi
   ```

### Veritabanı Ayarlarını Güncelleyin:
```bash
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'accounting');

   
