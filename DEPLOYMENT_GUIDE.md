# 📦 PANDUAN SETUP AUTO-DEPLOY GITHUB → RUMAHWEB

Panduan lengkap untuk setup automatic deployment dari GitHub ke hosting rumahweb.com.

---

## 🎯 Yang Akan Kita Setup

Setiap kali Anda push kode ke GitHub branch `main`, kode otomatis di-deploy ke website Anda di rumahweb.com tanpa perlu upload manual.

---

## 📋 STEP 1: Generate SSH Key (PENTING!)

Pertama, kita akan generate SSH key yang **aman** untuk GitHub Actions. Jangan gunakan password langsung.

### Di Rumahweb Panel (via SSH)

```bash
# Login ke server rumahweb via SSH
ssh livt5986@RW\ IJEN\ ENT

# Buat folder .ssh jika belum ada
mkdir -p ~/.ssh

# Generate SSH key baru (tekan Enter 3x untuk default)
ssh-keygen -t ed25519 -f ~/.ssh/github_actions -C "github-actions@livingkost.com"

# Lihat public key (copy ini nanti)
cat ~/.ssh/github_actions.pub

# Lihat private key (copy ini ke GitHub Secrets)
cat ~/.ssh/github_actions
```

### Tambahkan Public Key ke Authorized Keys

```bash
# Masuk ke server rumahweb (jika belum)
ssh livt5986@RW\ IJEN\ ENT

# Tambahkan public key
cat ~/.ssh/github_actions.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

---

## 🐙 STEP 2: Buat Repository GitHub

1. **Buat GitHub repository** di https://github.com/new
   - Nama: `Livingkost.com` (atau sesuai preferensi)
   - Visibility: **Private** (jika data sensitif) atau **Public**
   - Jangan initialize dengan README (kita sudah punya)

2. **Push project lokal ke GitHub:**

```bash
# Di folder /Users/handokodyanaditya/Documents/Livingkost.com

git config user.name "Nama Anda"
git config user.email "email@gmail.com"

git add .
git commit -m "Initial commit: Project setup"

git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/Livingkost.com.git
git push -u origin main
```

---

## 🔐 STEP 3: Setup GitHub Secrets

Ini adalah langkah PALING PENTING untuk keamanan!

1. **Buka repository di GitHub** → Settings → Secrets and variables → Actions
2. **Klik "New repository secret"** dan tambahkan 3 secrets:

### Secret 1: `SSH_PRIVATE_KEY`
- **Name:** `SSH_PRIVATE_KEY`
- **Value:** (Paste isi dari file `~/.ssh/github_actions` dari server rumahweb)
  - Mulai dari `-----BEGIN OPENSSH PRIVATE KEY-----` 
  - Sampai `-----END OPENSSH PRIVATE KEY-----`

### Secret 2: `SSH_USER`
- **Name:** `SSH_USER`
- **Value:** `livt5986`

### Secret 3: `SSH_HOST`
- **Name:** `SSH_HOST`
- **Value:** IP address server rumahweb (tanya ke support rumahweb jika tidak tahu)
  - Atau gunakan: `rwijen.web.id` (ganti dengan domain atau IP actual)
  - Cari di email/panel rumahweb Anda

**Contoh:**
```
SSH_PRIVATE_KEY = -----BEGIN OPENSSH PRIVATE KEY-----
                  ...isi key...
                  -----END OPENSSH PRIVATE KEY-----

SSH_USER = livt5986

SSH_HOST = 103.xx.xx.xx (atau domain rumahweb)
```

---

## 🚀 STEP 4: Setup Git Repository di Server Rumahweb

Sekarang kita setup agar server rumahweb bisa pull dari GitHub.

```bash
# Login ke server rumahweb
ssh livt5986@RW\ IJEN\ ENT

# Masuk ke folder public_html (atau folder website Anda)
cd ~/public_html

# Initialize git (jika belum)
git init
git config user.name "GitHub Actions"
git config user.email "noreply@github.com"

# Tambahkan remote GitHub
git remote add origin https://github.com/YOUR-USERNAME/Livingkost.com.git

# Fetch dari GitHub
git fetch origin main

# Checkout ke main branch
git checkout -b main origin/main
```

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

3. **Lihat GitHub Actions:**
   - Buka repository di GitHub
   - Klik tab "Actions"
   - Lihat apakah workflow "Deploy to Rumahweb" berjalan
   - Status harus ✅ (hijau)

4. **Verifikasi di website:**
   - Buka https://livingkost.com/
   - Lihat apakah perubahan Anda sudah muncul

---

## 🔧 Troubleshooting

### ❌ "Permission denied (publickey)"
- Pastikan public key sudah di `~/.ssh/authorized_keys` di server
- Pastikan SSH_PRIVATE_KEY secret sudah benar (full key dengan `-----BEGIN` dan `-----END`)

### ❌ "Repository not found"
- Pastikan SSH_USER dan SSH_HOST benar
- Test manual: `ssh livt5986@SSH_HOST` harus bisa connect

### ❌ "git: command not found"
- Server rumahweb mungkin belum install git
- Hubungi support rumahweb: "Saya butuh git di server"

### ❌ Deploy gagal, tapi ingin lihat error detail
- Buka tab "Actions" di GitHub
- Klik workflow yang gagal
- Klik "Deploy to Rumahweb" job
- Baca log untuk detail error

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
