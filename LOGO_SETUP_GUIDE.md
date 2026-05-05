# 🎓 Panduan Integrasi Logo MTs Al-Hidayah Tamansari

## ✅ Status Integrasi

Sistem ujian-mts sudah dikonfigurasi penuh untuk menggunakan logo MTs Al-Hidayah Tamansari. Semua view (navbar, footer, favicon, dll) sudah siap untuk menampilkan logo.

**Satu-satunya langkah yang tersisa**: Menyimpan file logo ke folder yang tepat.

---

## 📁 Cara Menyimpan Logo

### Lokasi Penyimpanan:
```
/workdir/www/ujian-mts/public/images/mts-al-hidayah-logo.png
```

### Langkah-Langkah:

#### **Opsi 1: Upload via File Manager / Explorer**
1. Buka file manager di komputer Anda
2. Navigate ke: `/workdir/www/ujian-mts/public/images/`
3. Drag-and-drop file logo ke folder tersebut
4. **Pastikan nama file**: `mts-al-hidayah-logo.png`

#### **Opsi 2: Copy File via Terminal**
```bash
# Jika logo sudah ada di lokasi lain, copy ke project:
cp /path/to/logo/file.png /workdir/www/ujian-mts/public/images/mts-al-hidayah-logo.png
```

#### **Opsi 3: Git Commit (untuk source control)**
```bash
cd /workdir/www/ujian-mts

# Simpan logo ke folder images
cp ~/Desktop/logo.png ./public/images/mts-al-hidayah-logo.png

# Add dan commit
git add public/images/mts-al-hidayah-logo.png
git commit -m "add: MTs Al-Hidayah Tamansari official logo"
git push
```

---

## 🖼️ Rekomendasi Format Logo

| Aspek | Rekomendasi |
|-------|------------|
| **Format File** | PNG dengan transparency (background transparan) |
| **Ukuran (px)** | Minimum 256x256, recommended 512x512 atau lebih |
| **Aspect Ratio** | Square (1:1) |
| **File Size** | < 100KB (untuk performa optimal) |
| **Nama File** | `mts-al-hidayah-logo.png` (wajib sesuai) |
| **Warna** | Full color (sudah ada di logo: hijau, merah, kuning) |

---

## 📍 Lokasi Logo Akan Ditampilkan

Setelah menyimpan logo, akan muncul di:

✅ **Navbar (Atas Kiri)** - Size 40x40px
- Visible pada setiap halaman saat user login
- Clickable untuk kembali ke dashboard

✅ **Footer (Bawah Kiri)** - Size 32x32px
- Visible pada setiap halaman
- Bersama nama sekolah dan copyright

✅ **Browser Tab** - Favicon
- Visible di tab browser saat membuka sistem
- Memudahkan user mengidentifikasi aplikasi

✅ **Mobile Home Screen** - Apple Touch Icon
- Jika user add website ke home screen iPhone/iPad
- Akan muncul dengan logo yang tepat

---

## 🔄 Fallback Behavior

**Jika logo tidak ditemukan**, sistem akan otomatis menggunakan default SVG icon (buku dengan api) sebagai fallback. UI akan tetap berfungsi normal, hanya tidak ada logo custom.

Untuk memastikan logo terbaca:
1. Pastikan nama file **TEPAT**: `mts-al-hidayah-logo.png`
2. Folder **TEPAT**: `/public/images/`
3. Clear browser cache: `Ctrl+Shift+R` atau `Cmd+Shift+R`

---

## ⚙️ Konfigurasi yang Sudah Dilakukan

Semua persiapan teknis sudah selesai:

✅ Navbar branding updated
✅ Footer branding updated
✅ Favicon meta tags added
✅ Apple touch icon configured
✅ Theme color meta tag set
✅ Fallback image handling implemented
✅ Dark mode logo display ready
✅ Views cached
✅ CSS compiled

---

## 🧪 Cara Verifikasi Logo Berhasil Ditampilkan

Setelah menyimpan logo:

1. **Refresh halaman**: `Ctrl+F5` atau `Cmd+Shift+R` (hard refresh)
2. **Cek Navbar**: Logo harus muncul di kiri atas (sebelah nama sekolah)
3. **Cek Footer**: Logo harus muncul di kiri bawah
4. **Cek Browser Tab**: Icon tab browser berubah
5. **Cek Developer Console**: 
   - Buka DevTools: `F12` atau `Ctrl+Shift+I`
   - Lihat di tab "Elements" → `<head>`
   - Cari `<link rel="icon">`

---

## 📝 Checklist

- [ ] Logo image disiapkan (PNG, 512x512px, < 100KB)
- [ ] Logo file disimpan ke `/public/images/mts-al-hidayah-logo.png`
- [ ] Browser cache di-clear (hard refresh)
- [ ] Logo muncul di navbar ✓
- [ ] Logo muncul di footer ✓
- [ ] Logo muncul di browser tab ✓
- [ ] Dark mode display tested ✓

---

## 🆘 Troubleshooting

### Logo tidak muncul?

1. **Periksa nama file**: Harus `mts-al-hidayah-logo.png` (case-sensitive)
2. **Periksa lokasi folder**: Harus di `/public/images/`
3. **Clear cache**: 
   - Browser cache: `Ctrl+Shift+Delete`
   - Hard refresh: `Ctrl+Shift+R`
   - Restart browser
4. **Periksa format**: Harus PNG (bukan JPG, GIF, dll)
5. **Periksa permission**: File harus readable oleh web server

### Logo muncul tapi pixel/blur?

- Logo terlalu kecil (< 256px)
- Solusi: Gunakan logo dengan ukuran lebih besar (512px atau lebih)

### Logo tidak terlihat di dark mode?

- Kemungkinan logo punya background gelap
- Solusi: Gunakan PNG dengan transparent background

---

## 📞 Support

Jika mengalami masalah:
1. Check logs: `/storage/logs/`
2. Verify file permissions: `ls -la public/images/`
3. Check browser console for errors: `F12` → Console tab

---

**Sistem siap untuk deployment di MTs Al-Hidayah Tamansari! 🚀**

*Last updated: May 6, 2026*
