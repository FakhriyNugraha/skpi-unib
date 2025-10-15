# Setup Google Drive API Credentials

## Persyaratan

Sebelum menggunakan fitur verifikasi Google Drive, Anda perlu mengatur credential Google Drive API dengan benar.

## Langkah-langkah Setup

### 1. Buat Project di Google Cloud Console

1. Kunjungi [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project yang sudah ada
3. Aktifkan Google Drive API:
   - Masuk ke **APIs & Services** → **Library**
   - Cari "Google Drive API"
   - Klik dan pilih **Enable**

### 2. Buat Service Account

1. Masuk ke **APIs & Services** → **Credentials**
2. Klik **Create Credentials** → **Service Account**
3. Isi detail service account:
   - **Name**: SKPI UNIB Service Account
   - **ID**: skpi-unib-service-account
   - **Description**: Service account untuk aplikasi SKPI UNIB
4. Klik **Create and Continue**

### 3. Berikan Permission ke Service Account

1. Pilih role **Editor** atau cukup **Viewer** jika hanya perlu akses baca
2. Klik **Continue** → **Done**

### 4. Buat dan Download Key

1. Klik service account yang baru dibuat
2. Masuk ke tab **Keys**
3. Klik **Add Key** → **Create New Key**
4. Pilih **JSON** format
5. Klik **Create**
6. File JSON akan terdownload secara otomatis

### 5. Konfigurasi File Credential

#### Untuk Local Development:
1. Simpan file JSON di direktori `storage/app/`
2. Rename file menjadi `skpiunib-credential.json`
3. Update `.env`:
   ```
   GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE=skpiunib-credential.json
   ```

#### Untuk Production (Laravel Cloud):
1. Encode file JSON ke Base64:
   ```bash
   # Linux/Mac:
   base64 -i storage/app/skpiunib-credential.json
   
   # Windows (PowerShell):
   [Convert]::ToBase64String([IO.File]::ReadAllBytes("storage/app/skpiunib-credential.json"))
   ```
2. Salin hasil Base64
3. Di Laravel Cloud Dashboard:
   - Masuk ke **Settings** → **Environment Variables**
   - Tambahkan:
     ```
     GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64=hasil_base64_disini
     ```

### 6. Berikan Akses ke Folder Google Drive

Agar service account dapat mengakses folder Google Drive milik mahasiswa:

1. Buka folder Google Drive yang ingin diakses
2. Klik tombol **Share**
3. Tambahkan email service account (dapat ditemukan di file JSON) dengan permission **Viewer**
4. Klik **Send** atau **Done**

Contoh email service account:
```
skpi-unib-service-account@your-project-id.iam.gserviceaccount.com
```

## Troubleshooting

### Error "File credentials Google Drive tidak ditemukan"

**Penyebab**: File credential tidak ditemukan atau environment variable tidak diatur dengan benar.

**Solusi**:
1. Pastikan file credential ada di lokasi yang benar
2. Pastikan environment variable diatur dengan benar:
   - Local: `GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE=skpiunib-credential.json`
   - Production: `GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64=base64_yang_benar`

### Error "EISDIR: Is a directory"

**Penyebab**: Path credential menunjuk ke direktori, bukan file.

**Solusi**:
1. Pastikan menggunakan Base64 approach di production
2. Jangan gunakan `GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE` di Laravel Cloud
3. Gunakan hanya `GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64`

### Error "Invalid base64"

**Penyebab**: Format Base64 tidak valid.

**Solusi**:
1. Pastikan Base64 diencode dengan benar tanpa karakter tambahan
2. Pastikan tidak ada line breaks atau spasi
3. Gunakan tool yang benar untuk encoding

## Keamanan

### Best Practices:
1. Jangan commit file credential ke repository
2. Gunakan Base64 approach untuk production
3. Batasi permission service account hanya ke yang diperlukan
4. Rotasi credential secara berkala
5. Monitor penggunaan API secara rutin

### File yang Harus Diabaikan:
Tambahkan ke `.gitignore`:
```
storage/app/*credential*.json
storage/app/*service_account*.json
.env
```

## Pengujian

Setelah setup selesai, uji koneksi dengan:

1. Jalankan aplikasi lokal:
   ```bash
   php artisan serve
   ```

2. Akses fitur verifikasi di aplikasi
3. Periksa log untuk error:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Bantuan Tambahan

Jika mengalami masalah:

1. Periksa log aplikasi untuk detail error
2. Pastikan credential memiliki permission yang cukup
3. Verifikasi bahwa service account sudah ditambahkan ke folder Google Drive
4. Hubungi administrator sistem jika perlu bantuan lebih lanjut