# HUC-WMS (Warehouse Management System)

A Laravel-based Warehouse Management System for Health Units and Centers (HUC).

## Prerequisites

Before running this system, ensure you have the following installed:

- **PHP 8.3 or higher** (with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- **Composer** (PHP dependency manager)
- **Node.js 18+ and npm** (for frontend assets)
- **MySQL 8.0+ or MariaDB** (database server)
- **XAMPP** (recommended for Windows, includes Apache, MySQL, and PHP)

## Installation and Setup

### 1. Project Setup

1. Clone or download this project to your XAMPP htdocs directory:
   ```
   c:\xampp\htdocs\HUC-WMS
   ```

2. Navigate to the project directory:
   ```
   cd c:\xampp\htdocs\HUC-WMS
   ```

### 2. Install Dependencies

Install PHP dependencies using Composer:
```
composer install
```

Install Node.js dependencies:
```
npm install
```

### 3. Environment Configuration

1. Copy the environment example file:
   ```
   copy .env.example .env
   ```

2. Open `.env` file and configure your database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=hucappdasmo
   DB_USERNAME=root
   DB_PASSWORD=  # Leave empty if no password set
   ```

### 4. Database Setup

1. Start XAMPP and ensure MySQL service is running.

2. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`) or use MySQL command line.

3. Create the database manually:
   ```sql
   CREATE DATABASE hucappdasmo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Import the database schema:
   ```
   mysql -u root -p hucappdasmo < database/schema/hucappdasmo.sql
   ```
   (Enter your MySQL root password when prompted)

5. Import initial seed data:
   ```
   mysql -u root -p hucappdasmo < database/seeders/InitialSeeder.sql
   ```
   (Enter your MySQL root password when prompted)

### 5. Laravel Setup

1. Generate application key:
   ```
   php artisan key:generate
   ```

2. Run database migrations (if needed):
   ```
   php artisan migrate
   ```

### 6. Build Frontend Assets

Build the frontend assets for production:
```
npm run build
```

Or for development with hot reload:
```
npm run dev
```

### 7. Start the Application

Start the Laravel development server:
```
php artisan serve
```

The application will be available at `http://localhost:8000`

Alternatively, since this is in XAMPP htdocs, you can access it via Apache at:
`http://localhost/HUC-WMS/public`

## Default Login Credentials

After seeding, you can log in with these default accounts:

- **Administrator**: username: `admin`, password: `password`
- **Head Pharmacist**: username: `hpharm`, password: `password`
- **Warehouse Staff**: username: `wstaff`, password: `password`
- **Health Center Staff**: username: `hstaff1`, password: `password`
- **Accounting Office**: username: `frank`, password: `password`

## Development

For development with auto-reload, use the built-in dev script:
```
composer run dev
```

This will start:
- Laravel server
- Queue worker
- Log tailing
- Vite dev server

## Testing

Run the test suite:
```
composer run test
```

## Troubleshooting

- **Database connection issues**: Ensure MySQL is running and credentials in `.env` are correct
- **Permission issues**: Make sure the `storage` and `bootstrap/cache` directories are writable
- **Assets not loading**: Run `npm run build` or `npm run dev`
- **Port conflicts**: If port 8000 is busy, use `php artisan serve --port=8080`

## License

This project is licensed under the MIT License.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
