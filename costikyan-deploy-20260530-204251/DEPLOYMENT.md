# Hostinger Deployment Guide — Costikyan Custom Carpet

## Prerequisites

- **Hostinger Plan**: Business Shared Hosting or higher (need PHP 8.3 + MySQL + SSH recommended)
- **Domain**: Pointed to Hostinger (or use the free subdomain during setup)
- **Local machine**: Node.js, npm, PHP 8.3, Composer installed

---

## Step 1 — Local Build Preparation

Run these commands in your project folder:

```bash
# 1. Install PHP dependencies (no dev packages for production)
composer install --no-dev --optimize-autoloader

# 2. Build frontend assets for production
npm run build

# 3. Clear all local caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## Step 2 — Create Production .env

Copy `.env.example` to `.env.production` and fill in your Hostinger values.

**Required changes from local:**

| Variable | Local Value | Production Value |
|----------|-------------|-------------------|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `APP_URL` | `http://localhost:8000` | `https://yourdomain.com` |
| `APP_KEY` | (blank) | Run `php artisan key:generate` |
| `DB_CONNECTION` | `mysql` | `mysql` |
| `DB_HOST` | `127.0.0.1` | Hostinger MySQL host (see cPanel) |
| `DB_PORT` | `3306` | `3306` |
| `DB_DATABASE` | `costikyan_carpet` | Your Hostinger DB name |
| `DB_USERNAME` | `root` | Your Hostinger DB user |
| `DB_PASSWORD` | (blank) | Your Hostinger DB password |
| `SESSION_DRIVER` | `database` | `database` or `file` |
| `CACHE_STORE` | `database` | `database` or `file` |
| `QUEUE_CONNECTION` | `database` | `database` or `sync` |
| `MAIL_MAILER` | `log` | `smtp` (configure with your email provider) |
| `STRIPE_KEY` | `your-stripe-publishable-key` | Your real Stripe publishable key |
| `STRIPE_SECRET` | `your-stripe-secret-key` | Your real Stripe secret key |
| `STRIPE_WEBHOOK_SECRET` | `your-stripe-webhook-secret` | Stripe webhook signing secret |

> **Note**: `APP_KEY` must be a fresh 32-character base64 string. Run `php artisan key:generate` locally first, then copy the generated key.

---

## Step 3 — Create MySQL Database on Hostinger

1. Log in to Hostinger hPanel
2. Go to **Databases → MySQL Databases**
3. Create a new database (e.g., `u123456789_costikyan`)
4. Create a database user and password
5. Add the user to the database with **ALL PRIVILEGES**
6. Copy the **Database Host** (usually something like `localhost` or `srv1234.hstgr.io`)

---

## Step 4 — Upload Files to Hostinger

### Option A — File Manager (Recommended for first deploy)

1. Zip your entire project folder locally (include `vendor/`, `public/build/`, everything except `node_modules/` and `.git/`)
2. In Hostinger hPanel, go to **Files → File Manager**
3. Navigate to `public_html/` (or your domain's root)
4. Upload the ZIP file
5. Extract the ZIP
6. **Important**: Move the `public/` folder contents to your web root, OR configure the document root to point to `public/` (see Step 5)

### Option B — FTP (For updates)

Use an FTP client (FileZilla, WinSCP):
- Host: your FTP server from Hostinger
- Port: 21
- Upload to `public_html/` or your domain folder

### Option C — Git Deployment (Business/Premium plans)

Hostinger supports Git. If SSH is enabled:
```bash
# On Hostinger via SSH
cd ~/public_html
git clone https://github.com/your-username/rug-website.git .
composer install --no-dev --optimize-autoloader
php artisan migrate --force
```

---

## Step 5 — Set Document Root to `public/`

Hostinger requires the web server to point to Laravel's `public/` folder.

### Method 1 — hPanel (Preferred)

1. Go to **Websites → Manage** next to your domain
2. Click **Advanced → Document Root**
3. Change from `public_html` to `public_html/public`
4. Save

### Method 2 — .htaccess (If Method 1 unavailable)

If you cannot change the document root, place this `.htaccess` in `public_html/`:

```apache
RewriteEngine On
RewriteRule ^$ public/ [L]
RewriteRule (.*) public/$1 [L]
```

---

## Step 6 — Set File Permissions

In Hostinger File Manager or via SSH:

```bash
# SSH into Hostinger
cd ~/public_html

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env

# Create storage symlink
php artisan storage:link
```

---

## Step 7 — Run Database Setup

Via Hostinger SSH or Terminal:

```bash
cd ~/public_html

# Copy production env
cp .env.production .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Run seeders (creates admin user, products, categories, coupons)
php artisan db:seed --force

# Optimize Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 8 — Configure Stripe for Production

1. Go to [Stripe Dashboard](https://dashboard.stripe.com)
2. Get your **Live Publishable Key** and **Live Secret Key**
3. Create a webhook endpoint:
   - URL: `https://yourdomain.com/stripe/webhook`
   - Events: `payment_intent.succeeded`, `invoice.payment_succeeded`, `checkout.session.completed`
4. Copy the **Webhook Signing Secret** to your `.env`

---

## Step 9 — SSL & HTTPS

1. In Hostinger hPanel, go to **SSL → Install SSL**
2. Hostinger provides free Let's Encrypt SSL
3. Ensure `APP_URL` in `.env` uses `https://`
4. (Optional) Force HTTPS by adding to `app/Providers/AppServiceProvider.php` boot method:
   ```php
   if ($this->app->environment('production')) {
       URL::forceScheme('https');
   }
   ```

---

## Step 10 — Post-Deploy Verification Checklist

| Check | How to Test |
|-------|-------------|
| Homepage loads | `https://yourdomain.com/` |
| Shop page loads | `https://yourdomain.com/shop` |
| Product detail works | `https://yourdomain.com/shop/{slug}` |
| Login works | `https://yourdomain.com/login` |
| Admin panel loads | `https://yourdomain.com/admin` (login as admin) |
| Trade portal loads | `https://yourdomain.com/trade-portal` (login as trade user) |
| Sitemap works | `https://yourdomain.com/sitemap.xml` |
| Robots.txt works | `https://yourdomain.com/robots.txt` |
| Images load | Check product images on shop page |
| Add to cart works | Test on a product page |
| Stripe checkout works | Complete a test purchase |

---

## Troubleshooting

### 500 Internal Server Error
- Check `storage/logs/laravel.log`
- Ensure `.env` exists and `APP_KEY` is set
- Verify `storage/` and `bootstrap/cache/` are writable (755)

### "No input file specified"
- Document root is not set to `public/`
- Missing `.htaccess` in `public/`

### CSS/JS not loading (404)
- Run `npm run build` locally before uploading
- Ensure `public/build/` folder exists in upload
- Check `VITE_MANIFEST` path in `.env`

### Database connection failed
- Verify DB credentials in `.env`
- Check Hostinger MySQL host (not always `localhost`)
- Ensure database user has ALL PRIVILEGES

### Images not showing
- Run `php artisan storage:link` on server
- Check `public/storage` symlink exists
- Verify `FILESYSTEM_DISK=local` or configure S3

---

## Quick Reference Commands

```bash
# After any code update on server:
php artisan migrate --force
php artisan optimize
php artisan view:cache

# Clear caches if something breaks:
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Check logs:
tail -f storage/logs/laravel.log
```

---

## Deployment Files Checklist

Ensure these are included in your upload:
- [ ] `app/` folder
- [ ] `bootstrap/` folder
- [ ] `config/` folder
- [ ] `database/` folder (migrations + seeders)
- [ ] `public/` folder (with `build/`, `.htaccess`, `index.php`)
- [ ] `resources/` folder (views)
- [ ] `routes/` folder
- [ ] `storage/` folder (empty structure, not SQLite file if using MySQL)
- [ ] `vendor/` folder
- [ ] `.env` (production)
- [ ] `artisan`
- [ ] `composer.json`

**Do NOT upload:**
- `node_modules/`
- `.git/` folder
- `tests/` (optional, not needed for production)
- Local SQLite file (if switching to MySQL)
