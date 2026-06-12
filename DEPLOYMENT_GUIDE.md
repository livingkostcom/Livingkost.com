# 📦 PANDUAN SETUP AUTO-DEPLOY GITHUB → RUMAHWEB (cPanel Version)

Panduan lengkap untuk setup automatic deployment dari GitHub ke hosting rumahweb.com menggunakan cPanel Git Version Control.

---

## 🎯 Yang Akan Kita Setup

Setiap kali Anda push kode ke GitHub branch `main`, kode otomatis di-deploy ke website Anda di rumahweb.com tanpa perlu upload manual.

**Metode:** cPanel Git + GitHub Webhook

---

## 📋 STEP 1: Clone Repository via cPanel Git

1. **Login ke cPanel** → Cari **"Git™ Version Control"**
2. Klik **"Create"**
3. Isi form:
   - **Repository URL:** `https://github.com/livingkostcom/Livingkost.com.git`
   - **Repository name:** `Livingkost`
   - **Clone directory:** `/home/livt5986/public_html` (sesuaikan path Anda)
   - Klik **"Create Repository"**

Tunggu sampai clone selesai. Repository GitHub akan ter-copy ke server Anda!

---

## 🐙 STEP 2: Setup Webhook di GitHub

Webhook adalah "notifikasi" dari GitHub ke server Anda untuk trigger deployment otomatis.

1. **Buka repository GitHub:** https://github.com/livingkostcom/Livingkost.com
2. **Pergi ke:** Settings → Webhooks → Add webhook
3. **Isi form:**
   - **Payload URL:** `https://livingkost.com/deploy.php`
   - **Content type:** `application/json`
   - **Events:** Pilih "Just the push event"
   - **Active:** ✅ Check
   - Klik **"Add webhook"**

---

## 🔐 STEP 3: Setup Secret di Webhook (OPTIONAL tapi RECOMMENDED)

Untuk keamanan lebih, kita bisa setup secret:

1. **Di GitHub Webhook settings:**
   - Scroll ke "Secret"
   - Generate secret: `openssl rand -hex 32` (di terminal lokal)
   - Paste di field "Secret"
   - Klik "Update webhook"

2. **Update file `deploy.php` di server:**
   - Ganti `'your-secret-key-here'` dengan secret yang Anda generate
   - Upload file yang sudah diupdate

---

## 📁 STEP 4: Upload `deploy.php`

File `deploy.php` sudah ada di project folder lokal. Anda perlu upload ke server:

**Option A: Via cPanel File Manager**
1. Login cPanel → File Manager
2. Buka folder `public_html`
3. Upload file `deploy.php` dari folder lokal Anda

**Option B: Via Git (Push ke GitHub, auto-deploy)**
1. File sudah siap di repository
2. Commit dan push ke GitHub
3. Webhook akan auto-pull file deploy.php

---

## ✅ STEP 5: Test Deploy

Sekarang test apakah deployment otomatis berfungsi:

1. **Edit satu file** di komputer lokal Anda
   ```bash
   # Edit misalnya public/index.html
   # Ubah sesuatu yang terlihat
   ```

2. **Push ke GitHub:**
   ```bash
   git add .
   git commit -m "Test auto-deploy"
   git push origin main
   ```

3. **Lihat di cPanel Git:**
   - cPanel → Git™ Version Control
   - Lihat repository `Livingkost`
   - Klik untuk lihat update terbaru
   - Harus ada commit terbaru Anda

4. **Verifikasi di website:**
   - Buka https://livingkost.com/
   - Lihat apakah perubahan Anda sudah muncul
   - Biasanya update dalam 1-2 detik

5. **Debug (jika ada issue):**
   - Buka `https://livingkost.com/deploy.log` untuk lihat log deployment
   - Cek webhook delivery: GitHub → Settings → Webhooks → Lihat "Recent Deliveries"

---

## 🔧 Troubleshooting

### ❌ Webhook tidak trigger deployment
**Solusi:**
1. Cek GitHub webhook delivery:
   - GitHub → Settings → Webhooks → Click webhook
   - Lihat "Recent Deliveries"
   - Status harus ✅ (green), bukan ❌ (red)
2. Cek file `deploy.log`:
   - Download via cPanel File Manager
   - Atau akses: `https://livingkost.com/deploy.log`
   - Lihat error apa yang terjadi

### ❌ "Repository already exists"
- Repository sudah ada di folder public_html
- Di cPanel: Delete repository dulu, atau gunakan folder lain
- Atau gunakan `git pull` untuk update (tidak perlu clone ulang)

### ❌ "git: command not found"
- Server rumahweb belum install git
- Hubungi support rumahweb: "Saya butuh Git di server untuk cPanel Git Version Control"

### ❌ File deploy.php tidak ditemukan
- Upload file `deploy.php` ke folder `public_html` via:
  - cPanel File Manager
  - Atau commit ke GitHub, webhook akan auto-pull

### ✅ Deploy berhasil, tapi website tidak update
- Refresh browser (Ctrl+Shift+R atau Cmd+Shift+R)
- Clear browser cache
- Tunggu 5 detik setelah push (webhook bisa butuh waktu)

---

## 📁 Struktur Folder Final

```
~/public_html/          (folder website di rumahweb)
├── public/
│   └── index.html
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── php/
│   └── api/
│       └── listings.php
├── .git/               (GitHub repository)
├── .github/workflows/
│   └── deploy.yml      (auto-deploy workflow)
└── README.md
```

---

## 🎉 Selesai!

Sekarang setiap kali Anda:
1. Edit kode lokal
2. `git push origin main`
3. Kode otomatis di-deploy ke https://livingkost.com/

**Tidak perlu upload manual lagi!** ✨

---

## 📞 Butuh Bantuan?

Jika ada error, cek:
1. **GitHub Actions log** → Workflow mana yang error
2. **Server SSH** → `ssh livt5986@server` & `cd ~/public_html && git status`
3. **Hubungi support rumahweb** jika git tidak terinstall

---

## 🔒 Security Tips

✅ **DO:**
- Generate SSH key dengan `ssh-keygen` (bukan pakai password)
- Simpan private key di GitHub Secrets (jangan commit!)
- Use HTTPS untuk clone jika tidak bisa SSH
- Regularly update keys

❌ **DON'T:**
- ❌ Commit private SSH key ke GitHub
- ❌ Share SSH password di mana-mana
- ❌ Use password authentication untuk automation (selalu SSH key)

---

**Dibuat:** 2024
**Untuk:** Livingkost.com
