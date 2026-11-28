# Laravel 6/8 → 13 Upgrade — Migration Log

Target: **Laravel 13 · PHP 8.3+**. Approach: incremental, one major per stage (`8 → 9 → 10 → 11 → 12 → 13`), test after each stage.

Full analysis: see the Phase 1–2 migration report (generated 2026-11-23).

---

## Stage 0 — Baseline & safety net ✅
- Confirmed app boots on **Laravel 8.83** in Docker (HTTP 200): PHP 7.4 + Apache, MariaDB 10.11, phpMyAdmin.
- DB backup: `backups/db_baseline_20261123.sql` (35 MB).
- `.env` backup: `backups/.env.l8-baseline`.
- Branch: `upgrade/laravel-13` created from the working baseline.
- `backups/` added to `.gitignore`.

### Changes made earlier this session to reach the L8 baseline
- `composer.json`: replaced 2 dead packages (`mehedi-iitdu/laravel-paystack` → `unicodeveloper/laravel-paystack`; `mehedi-iitdu/paypal` → `paypal/paypal-checkout-sdk`); added ~11 packages that were referenced in code/config but missing from the manifest; rewrote to a Laravel 8 stack.
- `app/Providers/AppServiceProvider.php`: guarded `Paginator::useBootstrap()` with `method_exists()` (version-safe).
- Added Docker environment (`docker-compose.yml`, `docker/`).

---

## Stage 1 — Laravel 8 → 9  ⏳ (in progress)
_PHP 8.0+ · Symfony 6 · Flysystem 3._

### Audit corrections (heuristic over-flagged)
On direct inspection, several "blockers" were false:
- `mehedi-iitdu/core-component-repository` → `illuminate/support: *` (agnostic) — **kept** (it is the vendor's ActiveITzone licensing/addon-check code).
- `anandsiddharth/laravel-paytm-wallet` v2 → `illuminate/support: >=8.0` (open) — **kept** (bumped to `^2.0`).
- Genuine blockers were only the PHP-7-only `laracon21/*` helpers, the dead `nexmo/laravel`, and `fideloper/proxy`.

### ⚠️ Security finding (important)
This is a **nulled/pirated** Active eCommerce CMS build. Two packages phone home:
- `laracon21/colorcodeconverter` — ~10% of calls silently POSTed the site domain to a hardcoded IP `206.189.81.181/insert_domain/`. **Removed** in the clean local replacement.
- `mehedi-iitdu/core-component-repository` — sends domain + addon purchase codes to `activeitzone.com` (vendor licensing). Expected for the commercial product; **kept**, flagged for awareness.

### Code changes applied
- Added clean local helpers (no backdoor): `app/Library/Combinations.php`, `Timezones.php`, `Colorcodeconverter.php`.
- `config/app.php`: removed 3 `laracon21` providers; registered the local classes as `Combinations` / `Timezones` / `Colorcodeconverter` aliases (call sites unchanged).
- `app/Http/Middleware/TrustProxies.php`: extends Laravel's built-in `TrustProxies`; replaced Symfony-6-removed `HEADER_X_FORWARDED_ALL` with the explicit header bitmask.
- `composer.json`: framework `^9.0`; passport `^11`, sanctum `^3`, ui `^4`, tinker `^2.7`, spatie/permission `^5.5`, dompdf `^1.0`, guzzle `^7.2`, ups-api `^2.0`, paytm `^2.0`; removed `fideloper/proxy`, `nexmo/laravel`, `laracon21/*`; dev: `spatie/laravel-ignition`, phpunit `^9.5`, collision `^6.1`.
- `docker/Dockerfile`: PHP 7.4 → **8.2** (covers L9–L12).
- TODO: remove 3 dead `use Nexmo;` imports (harmless; cleanup).

---

## Consolidation → direct to Laravel 13
Because Docker image builds in this environment are extremely slow (~20–40 min each; base-image pull alone ~19 min), and because closer inspection showed almost all packages are L13-compatible in their latest versions, the incremental 9→10→11→12 hops were consolidated into a **single jump to Laravel 13** (PHP 8.3). Only ONE image rebuild is needed (PHP 8.3); all `composer`/code iteration then runs fast inside the container.

### Genuine L13 blockers and how they're handled
- `unicodeveloper/laravel-paystack` (caps L11) and `kingflamez/laravelrave` (caps L10): **forked locally** into `packages/` with the `illuminate/support` constraint widened to include `^12|^13`; wired via Composer `path` repositories. Code unchanged, functionality preserved.
- `intervention/image`: pinned `^2.7` (runs on PHP 8.3, keeps the `Image::` facade — avoids the v3 API rewrite).
- `simplesoftwareio/simple-qrcode`: framework-agnostic (only needs bacon-qr-code) — kept.
- All first-party (`passport ^13`, `sanctum ^4`, `tinker ^3`, `ui ^4.6`, `socialite ^5.33`), plus `maatwebsite/excel ^4`, `spatie/laravel-permission ^8`, `barryvdh/*` v3, use their L13 releases.

### ✅ Laravel 13 REACHED
- `laravel/framework` **v13.43.0** installed on **PHP 8.3.37**, resolved cleanly with **no `--ignore-platform-reqs`**.
- Forked `unicodeveloper/laravel-paystack` + `kingflamez/laravelrave` into `packages/` (path repos, constraints widened to `^13`). Functionality preserved.
- `package:discover` succeeds; all providers load.
- **App boots and serves**: homepage `HTTP 200` (real storefront), `/login` `HTTP 200`.
- **Tests: 2 passed** (`Tests\Unit\ExampleTest`, `Tests\Feature\ExampleTest`). `phpunit.xml` migrated to the PHPUnit 11 schema.

### Code fixes applied for L13
- `app/Http/Helpers.php` — `getBaseURL()` now null-safe for `$_SERVER['HTTP_HOST']`/`SCRIPT_NAME` (PHP 8 undefined-key → was a 500 under tests/CLI).
- Route controller imports corrected (pre-existing wrong namespaces, dormant on L8, surfaced by `route:list`):
  `club_points.php` (namespace on groups), `offline_payment.php`, `african_pg.php`, `delivery_boy.php`, `paytm.php`, `seller_package.php` → point to the real `Api\V2\...` controllers.

### Remaining work (not blocking boot)
- **`route:list` Category B** — dead routes referencing controllers that exist nowhere (uninstalled Active eCommerce addons + install wizard): `AfricanPaymentGateway`, `Payment\Mpesa`, `Payment\Payfast`, `Payment\Toyyibpay`, `AuctionProduct(Bid)`, `Install`, `Update`, `Demo`. Pre-existing (non-functional on L8 too). **Decision needed:** install those addons, or remove the dead route files.
- **`env()` ×180 outside config** — refactor into `config/*` to allow `config:cache` in production.
- **Frontend** — still Laravel Mix + Vue 2; migrate to Vite + Vue 3 (separate track; does not affect backend boot).
- **Manual QA (Phase 5)** — payment gateway checkouts, admin CRUD, API V2, file uploads need manual verification; no automated coverage exists.
- **Production hardening** — `APP_DEBUG=false`, docroot → `public/`, `config:cache`/`route:cache`/`view:cache`, HTTPS.
