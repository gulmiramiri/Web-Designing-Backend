# PHP Blog Project

## Stack
- PHP (no framework, raw), MySQL, Tailwind CSS v4, vanilla JS
- DB: `php_project` (MySQL), PDO in pages, `mysqli` in API files
- Run under XAMPP at `http://localhost/PHP-PROJECT/`

## Key paths
- **Public pages**: `index.php`, `posts.php`, `detail.php?id=`, `catagory.php?id=`
- **Auth**: `auth/login.php`, `auth/register.php`, `auth/logout.php`
- **Admin** (requires session): `admin/` (CRUD for categories & posts)
- **API** (JSON): `api/posts.php?cat_id=&page=&limit=`, `api/category.php?id=`
- **Shared**: `functions/helpers.php`, `functions/pdo_conection.php`, `functions/checkSession.php`
- **JS frontend** (SPA-style): `blog-frontend/blog/` — static HTML + vanilla JS calling PHP API
- **Tailwind input**: `asset/css/input.css` → output: `asset/css/output.css`

## Dev commands
```bash
# Tailwind watch (CSS v4 via @tailwindcss/cli)
./node_modules/.bin/tailwindcss -i ./asset/css/input.css -o ./asset/css/output.css --watch
```

## DB quirks
- Table names: `catagories` (typo persisted), `posts`, `users` — all in `php_project` database
- Post `status`: `10` = enabled, `1` = disabled (check both in queries)
- Category table queried as `catagories` everywhere except `api/category.php` which uses `categories` — that endpoint is broken
- `BASE` URL hardcoded in `functions/helpers.php` as `http://localhost/PHP-PROJECT/`
- Image uploads: `asset/img/cat/`, `asset/img/posts/`

## Known bugs & quirks
- `api/posts.php`: `status = 1` in posts query vs `status = 10` in count query — inconsistent
- `api/category.php`: references `categories` table (does not exist; real table is `catagories`)
- `posts.php:60`, `detail.php:73`, `catagory.php:75` all link posts to `catagory.php?id=POST_ID` instead of `detail.php?id=POST_ID` — wrong route
- `admin/posts/delete.php` does **not** delete the image file from `asset/img/posts/` (edit.php does, but only when replacing)
- URL param naming is inconsistent: cat edit uses `cat_id`, post edit uses `i`, change-status uses `i` + `s`
- `session_start()` called both in `layouts/top_nav.php` and `functions/checkSession.php` — risk of "headers already sent"
- `o.css` and `ou.css` are loaded in all pages but have no clear purpose (possibly stale artifacts)
- `blog-frontend/blog/assets/js/api.js` has TODO stubs not yet connected to real API endpoints
