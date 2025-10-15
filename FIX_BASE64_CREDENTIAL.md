# Cara Memperbaiki Base64 Credential yang Salah

## Masalah Umum:
Error "invalid json for auth config" biasanya disebabkan oleh:
1. Base64 yang tidak valid (mengandung karakter ilegal)
2. Base64 yang rusak (kekurangan/karakter tambahan)
3. Konten yang bukan JSON yang valid setelah didecode

## Solusi Langkah demi Langkah:

### Langkah 1: Periksa File Credential Asli
```bash
# Di terminal lokal Anda:
cd C:\laragon\www\skpi-unib

# Periksa apakah file credential ada dan valid
ls -la storage/app/skpiunib-1cf5b8dee636.json

# Lihat isi file (pastikan ini adalah JSON yang valid)
head -n 20 storage/app/skpiunib-1cf5b8dee636.json
```

### Langkah 2: Encode Ulang dengan Benar

**Untuk Windows (PowerShell):**
```powershell
# Buka PowerShell di direktori project Anda
cd C:\laragon\www\skpi-unib

# Encode file ke Base64 dengan benar
$base64 = [Convert]::ToBase64String([IO.File]::ReadAllBytes("storage/app/skpiunib-1cf5b8dee636.json"))

# Simpan ke file sementara untuk mudah copy-paste
$base64 | Out-File -FilePath "credential_base64.txt" -Encoding ASCII

# Tampilkan hasilnya
Write-Host "Base64 Credential (disimpan di credential_base64.txt):"
Write-Host $base64
```

**Untuk Linux/Mac:**
```bash
# Di terminal
cd /path/ke/project/skpi-unib

# Encode dengan benar
base64 -i storage/app/skpiunib-1cf5b8dee636.json > credential_base64.txt

# Lihat hasilnya
cat credential_base64.txt
```

### Langkah 3: Validasi Base64

Sebelum menggunakan, pastikan Base64 valid:

1. **Panjang harus kelipatan 4**
2. **Hanya mengandung karakter**: A-Z, a-z, 0-9, +, /, =
3. **Tidak ada karakter aneh atau line breaks**

### Langkah 4: Perbarui Environment Variable di Laravel Cloud

1. Login ke Laravel Cloud Dashboard
2. Pilih aplikasi Anda
3. Masuk ke **Settings** → **Environment Variables**
4. Temukan atau tambahkan variable:
   ```
   GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_BASE64=[paste base64 yang benar di sini]
   ```
5. **HAPUS** atau **KOSONGKAN** variable `GOOGLE_DRIVE_SERVICE_ACCOUNT_FILE`

### Langkah 5: Deploy Ulang

```bash
# Commit perubahan (jika ada)
git add .
git commit -m "Fix Google Drive credential Base64"
git push origin main
```

## Debugging Tambahan:

### Jika Masih Error:

1. **Periksa Log Laravel Cloud**:
   ```bash
   # Di Laravel Cloud Dashboard, periksa Application Logs
   # Cari pesan error spesifik tentang Base64
   ```

2. **Validasi Manual Base64**:
   ```bash
   # Di terminal lokal, coba decode Base64 Anda:
   echo "base64_anda_disini" | base64 -d > test_credential.json
   
   # Periksa apakah hasilnya JSON yang valid:
   cat test_credential.json | jq .
   ```

3. **Pastikan Tidak Ada Karakter Tambahan**:
   - Hapus semua spasi, line breaks, atau karakter aneh
   - Base64 harus merupakan satu string panjang tanpa pemenggalan baris

## Contoh Base64 yang Valid:
Benar (perhatikan tidak ada line breaks):
```
ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsCiAgInByb2plY3RfaWQiOiAicHJvamVjdF9pZCIsCiAg...
```

Salah (ada line breaks/pemenggalan):
```
ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsCiAgInByb2plY3RfaWQiOiAicHJvamVjdF9p
ZCIsCiAg...
```

## Pencegahan untuk Mendatang:

1. **Gunakan tool yang benar untuk encoding**
2. **Jangan copy-paste dari editor teks biasa yang mungkin menambahkan karakter aneh**
3. **Selalu validasi Base64 sebelum menggunakan**
4. **Gunakan pendekatan file untuk development, Base64 hanya untuk production**