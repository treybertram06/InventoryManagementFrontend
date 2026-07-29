# Phone Inventory Management System

A PHP/MySQL web application for tracking refurbished phone inventory - intake, diagnostics/testing, grading, sales, and admin/user management.

## Software Requirements

- **PHP**: 8.1 or higher (required for backed enums in `models/user.php`)
  - `pdo_mysql` extension enabled
- **MySQL** or **MariaDB**
- A web server capable of running PHP, such as:
  - PHP's built-in development server (simplest option, no extra setup), or
  - Apache

## Database Name

`phone_inventory`

## Installation Steps

1. **Clone the repository** and move into the project directory.

2. **Create your config file** by copying the example:

   ```bash
   cp core/config.example.php core/config.php
   ```

   Edit `core/config.php` with your local MySQL credentials:

   ```php
   return [
       'db_host'    => 'localhost',
       'db_name'    => 'phone_inventory',
       'db_user'    => 'root',
       'db_pass'    => 'your_password',
       'db_charset' => 'utf8mb4',
       'theme'      => 'mono',
   ];
   ```

   `core/config.php` is gitignored.

3. **Import the database** (see below).

4. **Run the application** using PHP's built-in server from the project root:

   ```bash
   php -S localhost:8090 index.php
   ```

   Then visit **http://localhost:8090/** in a browser.

   > `index.php` acts as the front controller/router for all requests, so it must be passed as the router script. If you'd rather use Apache, point the document root at the project root where `index.php` lives.

## Database Import Instructions

From a terminal, with MySQL running:

```bash
mysql -u root -p < database/schema.sql
```

This creates the `phone_inventory` database and all required tables (it runs `CREATE DATABASE IF NOT EXISTS` internally, so no separate database-creation step is needed).

Optionally, load sample/seed data (device models, batches, devices, sales, etc.) so the app has something to display:

```bash
mysql -u root -p < database/testData.sql
```

This is safe to re-run because it truncates the relevant tables before reinserting data.

**Note:** `testData.sql` seeds three `user` rows (`admin`, `john.ramirez`, `sarah.lee`), but their `password_hash` values are dummy/placeholder hashes, you will not be able to log into those accounts.

## Login Credentials

There are no working seeded accounts. To get access:

1. Go to **/register** and create an account (username, email, password). New accounts are created with the `technician` role.
2. To test admin-only features (`/admin`), promote your account to admin directly in the database:

   ```sql
   UPDATE user SET role = 'admin' WHERE username = 'your_username';
   ```

Password requirements (enforced on registration): at least 8 characters, including an uppercase letter, a lowercase letter, and a digit.

## Additional Setup Instructions

- Static assets (icons, images) are served from `public/`.
- If the app can't connect to the database, it will return a `503 Service unavailable` response — double-check `core/config.php` credentials and that MySQL is running.