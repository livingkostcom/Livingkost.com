# Livingkost.com

Website untuk Livingkost - Platform untuk mencari rumah kost.

## Struktur Project

```
.
├── public/                 # File HTML utama
├── assets/
│   ├── css/               # File CSS
│   ├── js/                # File JavaScript
│   └── images/            # Gambar dan aset visual
├── php/                   # Backend PHP
├── .github/workflows/     # GitHub Actions (Auto-deploy)
└── README.md
```

## Teknologi

- Frontend: HTML5, CSS3, JavaScript
- Backend: PHP
- Hosting: Rumahweb.com
- CI/CD: GitHub Actions

## Setup Lokal

1. Clone repository:
   ```bash
   git clone https://github.com/your-username/Livingkost.com.git
   cd Livingkost.com
   ```

2. Push ke server:
   - GitHub Actions akan otomatis deploy saat ada push ke `main` branch

## Deployment

Deployment otomatis via GitHub Actions ke rumahweb hosting setiap kali:
- Push ke branch `main`
- Pull request di-merge

Lihat `.github/workflows/deploy.yml` untuk konfigurasi.
