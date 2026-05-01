# SS Crackers Core

Laravel 10 application powering the storefront, checkout flow, admin panel, and payment integrations for the SS Crackers project.

## Runtime Baseline

- PHP `8.2+`
- Composer `2.x`
- Node.js `18+`
- MySQL or MariaDB

The current lockfile and installed framework dependencies require PHP `>= 8.2`. Running this project on PHP `8.1` will fail Composer's platform checks.

## Local Setup

1. Configure PHP `8.2+`, Composer, Node.js, and a database.
2. Create `core/.env` from your environment template and fill database, mail, queue, storage, Shippo, and app settings.
3. Install backend dependencies:

```bash
composer install
```

4. Install frontend tooling when asset builds are needed:

```bash
npm install
npm run build
```

5. Generate the application key on fresh environments:

```bash
php artisan key:generate
```

6. Create the schema and bootstrap data:

```bash
php artisan migrate
php artisan db:seed
```

## Verification Checklist

1. Homepage renders.
2. Admin login page renders.
3. Cart add/update/remove works.
4. Checkout billing, shipping, and payment pages render.
5. One configured payment gateway can initialize.

## Operational Notes

- `../index.php` is the public entrypoint for the XAMPP root.
- Route definitions are split by domain under `core/routes/`.
- Shared settings data is cached through `App\Services\SettingsService`.
- Checkout/order pricing is centralized in `App\Services\OrderPricingService`.
- Order persistence and post-payment cleanup is centralized in `App\Services\CheckoutService`.
- Shippo configuration now comes from `config/services.php` via `SHIPPO_*` env values.

## Suggested Quality Checks

```bash
composer validate
php artisan test
```

## Source Control Hygiene

Do not commit:

- `core/.env`
- `core/vendor/`
- uploaded binaries under `assets/files/`
- generated sitemap files
- local SQL/ZIP backups
- OS metadata such as `.DS_Store`
