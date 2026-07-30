# ShopEase — Full-Stack Shop Website

A complete, responsive shop website built with PHP 8+, MySQL (PDO), vanilla
JavaScript, and Tailwind CSS. Includes a public storefront, user
authentication, a user dashboard, and a full admin panel for managing
products, categories, and users — plus a complete Dark Mode / Light Mode
system across every page.

## Requirements

- PHP 8.0+ with the PDO MySQL extension enabled
- MySQL 5.7+ / MariaDB 10.3+
- A web server (Apache/Nginx) or PHP's built-in server for local testing

## Setup

1. **Create the database.** Import the schema and sample data:

   ```bash
   mysql -u root -p < database/shop.sql
   ```

   (The database credentials — `shop` / `root` / no password — are already
   set in `config/database.php`. Edit that file if your environment differs.)

2. **Set real demo passwords.** `shop.sql` inserts two demo accounts with
   placeholder password hashes (since hashing must happen through PHP).
   Run this once to set the real, correctly hashed passwords:

   ```bash
   php database/seed_passwords.php
   ```

   This gives you:
   - **Admin** — username: `admin` / password: `Admin@123`
   - **User** — username: `johndoe` / password: `User@1234`

   Delete `database/seed_passwords.php` afterwards; it should not remain
   accessible in production.

3. **Set folder permissions.** Make sure the `uploads/` folder is writable
   by the web server:

   ```bash
   chmod -R 755 uploads
   ```

4. **Serve the project.** For local testing with PHP's built-in server:

   ```bash
   php -S localhost:8000
   ```

   Then open `http://localhost:8000` in your browser.

## Project Structure

See the top-level folders: `admin/` (admin panel pages), `api/` (JSON REST
endpoints), `config/` (database config), `includes/` (shared PHP partials:
header, footer, helper functions), `assets/` (CSS/JS), `uploads/` (uploaded
images), and `database/shop.sql` (full schema + sample data).

## Notes

- All API endpoints live under `api/` and use PDO prepared statements only.
- Sessions handle authentication; passwords are hashed with `password_hash()`
  (bcrypt).
- Dark mode is implemented with Tailwind's `class` strategy, persisted to
  `localStorage`, and falls back to the OS `prefers-color-scheme` when no
  preference has been saved yet.
- No build step is required — Tailwind is loaded via the CDN script and all
  JavaScript is vanilla ES6+, loaded directly as `<script>` tags.
