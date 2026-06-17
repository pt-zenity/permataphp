# Permata Switching - Pure PHP

Aplikasi **Permata Switching** adalah REST API untuk layanan Virtual Account (VA) SNAP BI, dibangun dengan **pure PHP 8.1 tanpa framework**.

---

## ⚠️ Catatan Keamanan Penting

Source code asli mengandung **kode berbahaya** yang telah **dihapus** pada versi ini:
- `eval(base64_decode(...))` di b2b.controller & inquiry.controller → mengirim IP/URL server ke Telegram
- `include/log.php` → backdoor yang mengirim seluruh data request (body, headers, session, cookies) ke bot Telegram pihak ketiga

**Versi ini bersih dari semua kode tersebut.**

---

## Endpoint

| Method | URL                        | Fungsi                        |
|--------|----------------------------|-------------------------------|
| GET    | `/`                        | Health check                  |
| POST   | `/access-token/b2b`        | Generate Access Token B2B     |
| POST   | `/transfer-va/inquiry`     | Inquiry Virtual Account       |
| POST   | `/transfer-va/payment`     | Payment Virtual Account       |

---

## Struktur Direktori

```
permata-switching/
├── index.php                   # Front controller / entry point
├── .htaccess                   # Rewrite rules (Apache)
├── nginx.conf.example          # Contoh konfigurasi Nginx
├── config/
│   ├── app.config.php          # Konfigurasi aplikasi
│   └── global_aut.php          # Kelas autentikasi global
├── include/
│   ├── database.php            # Bootstrap DB & session
│   ├── router.php              # Router pure PHP
│   ├── objdata.php             # PDO database wrapper
│   ├── func.mod.php            # Fungsi umum
│   ├── func.h2h.mod.php        # Fungsi H2H SNAP BI (bersih)
│   ├── iso8583.php             # Library ISO 8583
│   ├── definevar.mod.php       # Konstanta umum
│   ├── definevar.mbanking.php  # Konstanta mBanking
│   ├── definevar.tektaya.php   # Konstanta Tektaya
│   ├── assist.ini.php          # Konfigurasi DB (JANGAN commit!)
│   ├── df.php                  # Security guard
│   └── autoload/Classes/
│       ├── DataCDS/            # Class CDS
│       ├── mbanking/           # Class mBanking & TutupPPOB
│       └── tektaya/            # Class Tektaya
├── mvc/
│   ├── b2b/                    # Controller B2B Token
│   ├── inquiry/                # Controller Inquiry VA
│   └── payment/                # Controller Payment VA
└── logs/                       # Log file (auto-generated)
```

---

## Konfigurasi

### 1. Database
Edit file `include/assist.ini.php`:
```ini
ip=localhost
port=3306
database=bpr_web
user=Assist
password=YourPassword
```

Atau gunakan **environment variables**:
```bash
export DB_HOST=localhost
export DB_PORT=3306
export DB_NAME=bpr_web
export DB_USER=Assist
export DB_PASS=YourPassword
```

### 2. Web Server

**Apache** - sudah ada `.htaccess`, pastikan `mod_rewrite` aktif.

**Nginx** - gunakan `nginx.conf.example` sebagai template, ubah `root` dan `server_name`.

---

## Deployment

### Apache
```bash
# Copy ke web root
cp -r permata-switching/ /var/www/html/

# Set permissions
chown -R www-data:www-data /var/www/html/permata-switching
chmod -R 755 /var/www/html/permata-switching
chmod -R 777 /var/www/html/permata-switching/logs
chmod -R 777 /var/www/html/permata-switching/tmp

# Pastikan mod_rewrite aktif
a2enmod rewrite
systemctl restart apache2
```

### Nginx + PHP-FPM
```bash
# Copy konfigurasi
cp nginx.conf.example /etc/nginx/sites-available/permata-switching
# Edit server_name dan root path
nano /etc/nginx/sites-available/permata-switching
ln -s /etc/nginx/sites-available/permata-switching /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

# PHP-FPM 8.1
systemctl start php8.1-fpm
```

### Test
```bash
# Health check
curl http://localhost/

# Test B2B Token
curl -X POST http://localhost/access-token/b2b \
  -H "Content-Type: application/json" \
  -H "x-client-key: YOUR_CLIENT_KEY_HERE_ATLEAST17CHARS" \
  -H "x-timestamp: 2024-01-01T00:00:00+07:00" \
  -H "x-signature: your_signature" \
  -d '{"grantType":"client_credentials"}'
```

---

## Requirements
- PHP 8.1+
- Extensions: `pdo`, `pdo_mysql`, `curl`, `mbstring`, `json`, `openssl`
- Web server: Apache 2.4+ atau Nginx 1.18+
- MySQL / MariaDB

---

## Versi
- **v1.0** - Pure PHP tanpa framework, bersih dari backdoor
