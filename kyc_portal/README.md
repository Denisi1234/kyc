# KYC Choir Portal

Production-ready Laravel 12 member portal for Kijitonyama Youth Choir.

The repository includes `vendor/` and the compiled Vite assets in `public/build`, so it can be deployed on cPanel shared hosting without running Composer or npm on the server.

## Requirements

- PHP 8.2 or newer with PDO MySQL, mbstring, openssl, fileinfo, ctype, json and tokenizer
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite`
- HTTPS

## What is included

- Full Laravel application, including `vendor/`
- Compiled portal assets in `public/build`
- Database migrations and seeders (roles, permissions, settings and sample events)
- A one-time browser setup helper: `public/kyc-setup-7586104f.php`
- A production `.env.example` (no secrets are committed)

## Deploy on cPanel

### 1. Create the database

In cPanel, open **MySQL Databases** and create:

- A database, for example `kyc_portal`
- A database user with a strong password
- Grant the user all privileges on the database

Write down the database name, username and password.

### 2. Clone the repository

Use cPanel **Git Version Control** or Terminal to clone the private repository into:

```text
/home/alheqffk/kyc_portal
```

Do not clone it into `public_html` or into the domain document root. The application must stay outside the web root.

### 3. Expose the public folder

The portal must be reachable at `/portal`. Choose one option.

**Preferred: symlink**

```bash
ln -s /home/alheqffk/kyc_portal/public /home/alheqffk/kijitonyamayouthchoir.co.tz/portal
```

If the document root is `public_html`, use that path instead:

```bash
ln -s /home/alheqffk/kyc_portal/public /home/alheqffk/public_html/portal
```

**Alternative: copy the public files**

Create the folder `<document-root>/portal` and copy everything from `/home/alheqffk/kyc_portal/public` into it. Repeat the copy after any update that changes files in `public/`.

### 4. Run the one-time setup

Open:

```text
https://kijitonyamayouthchoir.co.tz/portal/kyc-setup-7586104f.php?key=3c91fad48562e7b0
```

Enter the portal URL, database details and the first System Admin account. The helper will:

- Create `.env` with a generated `APP_KEY`
- Set `PUBLIC_PATH` to the real public folder
- Create `public/storage` and `storage/app/public`
- Run all migrations
- Seed roles, permissions, settings and sample events
- Create the first System Admin
- Clear Laravel caches

### 5. Finish the deployment

1. Delete `public/kyc-setup-7586104f.php` from the server.
2. Sign in at `https://kijitonyamayouthchoir.co.tz/portal/login`.
3. Change the temporary administrator password.
4. Create a second System Admin account (recommended).
5. Configure SMTP in `.env` if password-reset emails should be delivered.
6. Make sure `.env` is not readable by other users (`chmod 600 .env`).

## File permissions

Laravel needs write access to:

```text
storage/
bootstrap/cache/
```

The web server user must be able to write to those folders. On cPanel, `755` for folders is usually correct; use `775` only if the host requires it. Do not use `777`.

## Useful commands

Run these from `/home/alheqffk/kyc_portal` if cPanel Terminal is available:

```bash
php artisan migrate:status
php artisan optimize:clear
php artisan portal:create-admin --name="Second Admin" --email="admin2@example.com" --password="A-strong-password"
```

## Local development

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Then open `http://127.0.0.1:8000`.

## Security notes

- Never commit `.env` or database passwords.
- Delete the setup helper immediately after use.
- Keep the repository private.
- Use HTTPS only.
- Keep `vendor/` and `public/build` in the repository for cPanel deployment; update them before pushing dependency or front-end changes.
