# URL Shortener

A Laravel-based URL shortening service with role-based access control and multi-company support.

---

## Requirements

Make sure you have the following installed on your system:

- PHP 8.2+
- Composer
- MySQL
- Node.js & NPM
- Git

---

## Setup Instructions

### Step 1 — Clone the Repository

Open your terminal and run:

```bash
git clone https://github.com/your-username/url-shortener.git
cd url-shortener
```

---

### Step 2 — Install PHP Dependencies

```bash
composer install
```

This will install all Laravel and PHP packages listed in `composer.json`.

---

### Step 3 — Install Node Dependencies & Build Assets

```bash
npm install
npm run build
```

This will install frontend packages and compile CSS/JS assets.

---

### Step 4 — Environment Setup

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

---

### Step 5 — Configure Database

Open the `.env` file and update the database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=
```

> If you are using Laragon, the default username is `root` and password is empty.

---

### Step 6 — Create Database

Open **phpMyAdmin** or any MySQL client and create a new database:

```sql
CREATE DATABASE url_shortener;
```

---

### Step 7 — Run Migrations & Seed

```bash
php artisan migrate --seed
```

This will:
- Create all required tables (`users`, `companies`, `short_urls`, `invitations`)
- Seed a default **SuperAdmin** account using raw SQL

---

### Step 8 — Start the Development Server

```bash
php artisan serve
```

Visit the application at:

```
http://127.0.0.1:8000
```

---

## Default SuperAdmin Credentials

Use these credentials to log in as SuperAdmin:

```
Email:    superadmin@example.com
Password: password
```

---

## How It Works

### Roles & Permissions

| Role       | Create URL | View URLs                | Invite Users                  |
|------------|------------|--------------------------|-------------------------------|
| SuperAdmin | ❌         | All companies            | Admin (creates new company)   |
| Admin      | ✅         | Own company only         | Admin & Member                |
| Member     | ✅         | Own URLs only            | ❌                            |

---

### Invitation Flow

1. **SuperAdmin** logs in → clicks "Invite User"
2. Enters a **new company name** + admin email
3. A new company is created and an invitation token is generated
4. The invitation link is displayed on the page with a **Copy Link** button
5. Share the copied link with the invited user manually
6. Invited user opens the link → fills name & password → registers
7. User is automatically assigned the correct role and company

> The assessment did not specify any particular mechanism for sharing invitation links (e.g. email). We implemented an on-page link display with a copy button for simplicity and ease of testing.

> Same flow applies when Admin invites an Admin or Member into their company.

---

### Short URL Flow

1. Admin or Member logs in → clicks "Create Short URL"
2. Enters the original URL (e.g. `https://google.com`)
3. A random 6-character short code is generated (e.g. `aB3xYz`)
4. Short URL is publicly accessible:
   ```
   http://127.0.0.1:8000/s/aB3xYz
   ```
5. Anyone can visit the short URL — it redirects to the original URL without login

---

## Running Tests

Tests use an **SQLite in-memory database** — your real MySQL data is completely safe.

```bash
php artisan test --filter ShortUrlTest
```

### Test Coverage

| Test | Description |
|------|-------------|
| ✅ Admin can create short URL | Admin gets redirected after creating URL |
| ✅ Member can create short URL | Member gets redirected after creating URL |
| ✅ SuperAdmin cannot create short URL | SuperAdmin gets 403 Forbidden |
| ✅ Admin can only see own company URLs | Other company URLs are hidden |
| ✅ Member can only see own URLs | Other members URLs are hidden |
| ✅ Short URLs are publicly resolvable | Redirects without login |

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── InvitationController.php   — Handles invitations
│   │   └── ShortUrlController.php     — Handles short URLs
│   └── Middleware/
│       └── RoleMiddleware.php         — Role-based access control
├── Models/
│   ├── Company.php
│   ├── Invitation.php
│   ├── ShortUrl.php
│   └── User.php
database/
├── migrations/                        — All table migrations
└── seeders/
    └── SuperAdminSeeder.php           — Seeds SuperAdmin via raw SQL
resources/views/
├── dashboard.blade.php
├── invitations/
│   ├── create.blade.php
│   └── accept.blade.php
└── short_urls/
    ├── index.blade.php
    └── create.blade.php
```

---

## AI Tools Used

- **Amazon Q** — Laravel middleware syntax lookup, debugging 419 CSRF error in tests
