# VELIN

VELIN is a night bookstore for the Aptech eProject. It is a Laravel shop: catalog, cart, checkout, reviews, wishlist, delivery tracking, and an editor desk.

The shop voice is a reading room in Kala Ghoda. The catalog is original, so the project does not borrow covers or text from real publishers.

## Run it

Requirements: PHP 8.3+ with `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, and `fileinfo`. Composer dependencies are already installed in `vendor`.

```bash
cd D:\BookStore
php artisan migrate --seed --force
php artisan storage:link
php artisan serve
```

Open http://127.0.0.1:8000

The database is SQLite at `database/database.sqlite`. No MySQL setup is required. Prices are in INR and include GST. Dispatch is ₹49, and free from ₹999.

## Classroom accounts

| Desk | Email | Password |
| --- | --- | --- |
| Reader | reader@velin.store | Reader#2026 |
| Reader | meera@velin.store | Meera#2026 |
| Editor | admin@velin.store | VelinAdmin#2026 |

On a local machine the sign-in page also offers Google and GitHub buttons. They sign in a demo social profile on this computer. They are not a live OAuth handshake. Add `GOOGLE_CLIENT_ID` or `GITHUB_CLIENT_ID` only when you are ready to install Laravel Socialite and finish that redirect.

Password reset emails are written to `storage/logs/laravel.log`. Locally, the reset screen also prints the link so the flow can be marked without an inbox.

A rehearsal card number is `4242 4242 4242 4242`. The application checks it, then stores only the brand and the last four digits.

## What the brief asked for

| Requirement | Where it lives |
| --- | --- |
| Register, sign in, sign out | `/register`, `/login`, profile sign-out |
| Social account | Local Google and GitHub demo profiles, with a documented place for real OAuth keys |
| Password reset | `/forgot-password` |
| Catalog with title, author, genre, description, cover, price | `/books` and each book page |
| Genres and authors | Filters, genre index, editor desk |
| Bestsellers and new arrivals | Home, and `?collection=bestsellers` or `?collection=new` |
| Search by title, author, genre | Catalog search, including ISBN |
| Sort by price, popularity, release date | Catalog sort |
| Profile, addresses, payment methods | `/account` |
| Cart and quantities and total | `/cart` |
| Ratings, reviews, likes | Book page |
| Order history, status, delivery tracking | `/account/orders` |
| Admin books, readers, orders | `/admin` |
| Wishlist | Heart / Save, and `/wishlist` |
| Errors, security, docs | Custom error pages, hashed passwords, CSRF, headers, this file and `docs/` |

## Record the walkthrough

A recorded demo already lives at `docs/video/velin-walkthrough.webm` (MP4 when converted). Developer Word doc with snapshots: `docs/VELIN-Developer-Documentation.docx`.

To re-record from `docs/tools` while `php artisan serve` is running:

```bash
cd docs/tools
npm run walkthrough
npm run docx
```

Manual checklist for http://127.0.0.1:8000:

1. Home, then search “Salt”, then filter Thriller and sort by price.
2. Open a book, add it to the cart, save it to the wishlist.
3. Sign in as `reader@velin.store`, adjust the quantity, and check out with the saved address.
4. Open the new order and show the timeline.
5. Leave a review and like Meera’s note on The Salt Atlas.
6. Sign out, sign in as `admin@velin.store`, change a stock count, and move the new order to Shipped with a tracking code.
7. Sign back in as the reader and show the tracking code on the order.

## Tests

```bash
php artisan test
```

## Project map

- `app/Models` — readers, books, carts, orders
- `app/Services` — cart, checkout, addresses, payment vault
- `app/Http/Controllers/Store` — the shop
- `app/Http/Controllers/Account` — profile and orders
- `app/Http/Controllers/Admin` — the editor desk
- `resources/views` — Blade pages
- `public/css/velin.css` and `public/js/velin.js` — the interface, no Node build required
- `docs/USER_GUIDE.md` and `docs/DEVELOPER.md`
