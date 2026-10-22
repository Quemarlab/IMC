# Young Masters LTD — website & admin panel

Public website plus a custom admin panel for a Rwanda mining consultancy (Mining · OHS · Environment).
PHP 8.1+, MySQL/MariaDB, Bootstrap 5. No framework, no build step.

## Setup

1. Copy `.env.example` to `.env` and fill in the database and SMTP values (`.env` is git-ignored).
2. Create the database (default name `ym`), then install the schema and seed the services:
   ```
   php database/install.php --admin=you@example.com --name="Your Name"
   ```
   It prints a one-time password for the admin account. Add `--migrate-legacy` to import data from the old
   IMC-era tables (`project`, `news`, `file`, `gallery`, `contact`, `newslater`, `account_holders`); the legacy tables are never modified.
3. Open `/ControlPanel/` and sign in. Fill in **Site settings** (phone, email, address, social links, map).

### Removing the old IMC-era tables

```
php database/cleanup-legacy.php                # dry run: lists every table the app does not use
php database/cleanup-legacy.php --confirm      # writes storage/backups/legacy-tables-*.sql, then drops them
php database/cleanup-legacy.php --confirm --keep=data,settings   # protect tables you still need
```

Run `install.php --migrate-legacy` first if you still want the old data. The backup file restores anything dropped.

The admin **Backup** page downloads a full SQL dump of the site tables.

For production set `APP_ENV=production`, `APP_DEBUG=false`, serve over HTTPS and enable the HSTS line in `.htaccess`.

## Layout

| Path | Purpose |
|---|---|
| `index.php`, `about.php`, `services.php`, `projects.php`, `news.php`, `gallery.php`, `contact.php` … | Public pages (pretty URLs via `.htaccess`) |
| `inc/` | Public layout (header/footer) and card partials |
| `api/` | JSON endpoints: contact form, newsletter |
| `ControlPanel/` | Admin panel (`login`, `dashboard`, `resource` = projects/news/services/gallery CRUD, `messages`, `subscribers`, `users`, `settings`, `profile`, `activity`) |
| `ControlPanel/inc/resources.php` | Declarative field definitions for the content types — add a field here and the form, validation and list pick it up |
| `ControlPanel/upload/` | User uploads (script execution disabled) |
| `app/Core/` | Env, DB, Session, Csrf, Auth, Upload, Sanitizer, Mailer, Validator, RateLimiter, Settings, Audit |
| `app/Repositories/` | Data access (one class per table group) |
| `database/` | `schema.sql` and the CLI installer / legacy migrator |
| `storage/logs/` | Application error log |

## Shared accounts (website admin + UMP)

The website admin and the UMP production system (separate repository) run on **one database** and share accounts, passwords,
two-factor authentication, mail settings and the security settings. **Users** (this admin → Users) is where people are created and given a
role in each system: Website (Administrator / Editor) and UMP (Administrator / Operator / Viewer). New people can be emailed an invitation link
to choose their own password. Set `UMP_URL` in `.env` for the "Open UMP" sidebar shortcut. Platform changes live in `database/migrations/platform_*.php`
(identical copies in the UMP repository); `php database/install.php` applies them.

Security settings (Site settings → Security) apply to both systems: inactivity timeout, lockout, minimum password length, and
"require two-factor for administrators".
## Security model

- Secrets live only in `.env`. `app/`, `database/`, `storage/` and dotfiles are blocked by `.htaccess`.
- Sessions: HttpOnly, SameSite=Lax, regenerated on login, 2-hour idle timeout. Login is rate-limited **server-side** (5 failures / 15 min per account, 15 per IP).
- Every state-changing request carries a CSRF token; logout is POST-only.
- All SQL is parameterised; all output is escaped; rich text is passed through an allow-list sanitizer on save.
- Uploads: size cap, real MIME sniffing, random filenames, re-encoded when GD is available, no script execution in the upload folder.
- Public forms have honeypot + per-IP rate limits; messages are stored before any email is attempted.
- Roles: **admin** (everything) and **editor** (content and messages). Sensitive actions are written to the activity log.
