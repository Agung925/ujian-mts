# MTs Al-Hidayah Tamansari Branding Assets

## Logo Placement

**Lokasi**: `/public/images/mts-al-hidayah-logo.png`

### File yang diperlukan:
- **mts-al-hidayah-logo.png** - Logo MTs Al-Hidayah Tamansari (recommended: 512x512px atau lebih, PNG format dengan transparency)

### Lokasi penggunaan di project:

1. **Navbar (top-left)**
   - File: `resources/views/layouts/navigation.blade.php`
   - Size: 40x40px (displayed at 36x36px)
   - Location: Next to school name

2. **Footer**
   - File: `resources/views/layouts/app.blade.php`
   - Size: 32x32px (displayed at 28x28px)
   - Location: Footer left side

3. **Favicon**
   - File: `resources/views/layouts/app.blade.php`
   - Meta tags untuk browser tab & Apple devices

### Installation:

1. Save the logo image to this directory as `mts-al-hidayah-logo.png`
2. Clear browser cache or perform hard refresh (Ctrl+Shift+R / Cmd+Shift+R)
3. Logo akan otomatis appear di navbar, footer, dan browser tab

### Fallback behavior:

Jika file logo tidak ditemukan, sistem akan otomatis menggunakan default SVG icon (book with torch) sebagai fallback. Ini memastikan UI tetap berfungsi dengan baik.

### Image recommendations:

- **Format**: PNG dengan transparency (background transparan)
- **Size**: Minimum 256x256px, recommended 512x512px atau lebih
- **Aspect Ratio**: Square (1:1)
- **File Size**: < 100KB (untuk performance)
- **Color**: Full color (logo sudah memiliki warna green dan lainnya)

### Logo used in:

✅ Browser tab icon (favicon)
✅ Navbar branding
✅ Footer branding
✅ Apple touch icon (untuk mobile home screen)

---

*Last updated: May 6, 2026*
