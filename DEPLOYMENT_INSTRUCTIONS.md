# Instruksi Deployment ke Laravel Cloud

## Persiapan Environment Variables untuk Laravel Cloud

### 1. Siapkan Google Service Account Credential

#### Langkah 1: Encode file credential ke Base64
```bash
# Di terminal lokal Anda:
base64 -i storage/app/skpiunib-1cf5b8dee636.json
```

#### Langkah 2: Salin hasil base64
Salin seluruh output base64 yang dihasilkan.

### 2. Konfigurasi Environment Variables di Laravel Cloud

#### Langkah 1: Akses Laravel Cloud Dashboard
1. Login ke [Laravel Cloud](https://forge.laravel.com)
2. Pilih aplikasi Anda
3. Masuk ke menu **Settings** → **Environment Variables**

#### Langkah 2: Tambahkan Environment Variables Berikut
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.laravel.cloud

# Database akan disediakan otomatis oleh Laravel Cloud
# DB_* variables biasanya sudah diatur

# Google Drive Credentials (BASE64 APPROACH - WAJIB UNTUK LARAVEL CLOUD)
GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64=base64_yang_sudah_anda_encode_disini

# Jika menggunakan Redis (opsional tapi direkomendasikan)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Logging untuk production
LOG_LEVEL=warning
```

#### Langkah 3: Hapus atau Kosongkan Variabel yang Tidak Digunakan
Pastikan variabel berikut **TIDAK** ada atau dikosongkan:
```
# JANGAN GUNAKAN INI DI LARAVEL CLOUD
# GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE=skpiunib-1cf5b8dee636.json
```

### 3. Deploy Aplikasi

#### Langkah 1: Commit Perubahan
```bash
git add .
git commit -m "Update deployment configuration for Laravel Cloud"
git push origin main
```

#### Langhh 2: Trigger Deployment
Di Laravel Cloud Dashboard:
1. Pergi ke menu **Deployment**
2. Klik **Deploy Now** atau tunggu auto-deployment

### 4. Troubleshooting

#### Jika Terjadi Error "File credentials Google Drive tidak ditemukan":

1. **Pastikan Base64 Environment Variable Sudah Diatur**:
   - Periksa di Laravel Cloud Dashboard → Settings → Environment Variables
   - Pastikan `GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64` sudah diisi dengan benar

2. **Periksa Format Base64**:
   - Pastikan tidak ada karakter tambahan atau spasi
   - Base64 harus merupakan satu string panjang tanpa line breaks

3. **Periksa Permission File**:
   - File credential harus memiliki permission yang benar
   - Jika menggunakan file approach, pastikan file ada di `storage/app/`

#### Jika Terjadi Error "EISDIR: Is a directory":

1. **Pastikan Menggunakan Base64 Approach**:
   - Jangan gunakan `GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE` di Laravel Cloud
   - Gunakan hanya `GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64`

2. **Periksa Environment Variables**:
   - Pastikan tidak ada variabel yang saling bertentangan
   - Pastikan hanya satu approach yang digunakan

### 5. Konfigurasi Tambahan (Opsional)

#### Untuk Mail Configuration:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### Untuk File Storage (jika menggunakan S3):
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=your_bucket_name
```

### 6. Monitoring dan Logging

#### Periksa Logs:
```bash
# Di Laravel Cloud, gunakan dashboard untuk melihat logs
# Atau gunakan command line jika tersedia:
php artisan tail
```

#### Periksa Health Check:
- Akses endpoint health check jika tersedia
- Monitor application performance metrics

### 7. Rollback (Jika Diperlukan)

Jika terjadi masalah setelah deployment:
1. Gunakan Laravel Cloud rollback feature
2. Atau deploy branch/tag sebelumnya

---

**Catatan Penting**: 
- Jangan pernah commit file `.env` ke repository
- Jangan pernah commit credential Google Drive ke repository
- Selalu gunakan Base64 approach untuk Laravel Cloud
- Pastikan environment variables diatur dengan benar sebelum deployment