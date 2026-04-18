# Laravel 12 Custom Auth Starter

A custom starter project for Laravel 12 with Laravel UI 4.6 authentication, featuring user approval workflow and role-based access control.

## Features

- **Laravel 12** framework with latest features
- **Laravel UI 4.6** for authentication scaffolding
- **Custom Authentication Flow**:
  - After registration, users are redirected to login page (not dashboard)
  - New users require admin approval before they can log in
  - Unapproved users see appropriate messages
- **Role-Based Access**:
  - Admin users (`is_admin = 1`)
  - Approved users (`is_approved = 1`)
  - User approval system
- **Modern Frontend Stack**:
  - Tailwind CSS v4
  - Bootstrap 5
  - Vite for asset bundling
- **Testing Ready**: Pest PHP testing framework included

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL/PostgreSQL/SQLite database

## Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd laravel12_with_custom_auth
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**:
   ```bash
   npm install
   ```

4. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**:
   - Configure your database in `.env` file
   - Run migrations:
     ```bash
     php artisan migrate
     ```

6. **Build Assets**:
   ```bash
   npm run build
   ```

## Quick Setup (Alternative)

You can use the built-in setup script:
```bash
composer run setup
```

This will install dependencies, copy env file, generate key, run migrations, and build assets.

## Running the Application

1. **Start the development server**:
   ```bash
   php artisan serve
   ```

2. **Start asset watcher** (in another terminal):
   ```bash
   npm run dev
   ```

3. **Access the application**:
   - Open `http://localhost:8000` in your browser

## Usage

### User Registration & Approval Workflow

1. **Register a new account**:
   - Visit `/register`
   - Fill in the registration form
   - After successful registration, you'll be redirected to the login page with a success message

2. **Admin Approval**:
   - Admin users need to approve new registrations
   - Access admin panel at `/adminPage` (requires authentication and admin role)
   - Approve users by updating their `is_approved` status in the database

3. **Login**:
   - Visit `/login`
   - If your account is not approved, you'll see an error message
   - Once approved by admin, you can log in successfully

### User Roles

- **Regular Users**: Can access basic pages after approval
- **Admin Users**: Have access to admin panel and can approve other users

### Available Routes

- `/` - Welcome page
- `/login` - User login
- `/register` - User registration
- `/home` - Dashboard (authenticated users)
- `/userPage` - User-specific page (authenticated users)
- `/adminPage` - Admin panel (admin users only)
- `/test` - Test page

## Development

### Adding New Features

1. **Controllers**: Create new controllers in `app/Http/Controllers/`
2. **Routes**: Add routes in `routes/web.php`
3. **Views**: Create Blade templates in `resources/views/`
4. **Models**: Add Eloquent models in `app/Models/`
5. **Migrations**: Create database migrations with `php artisan make:migration`

### Testing

Run tests with Pest:
```bash
php artisan test
```

### Code Quality

Format code with Laravel Pint:
```bash
vendor/bin/pint --dirty --format agent
```

## Database Schema

The `users` table includes additional columns:
- `is_admin` (boolean): Marks admin users
- `is_approved` (boolean): Marks approved users

## Customization

- **Authentication Logic**: Modify `app/Http/Controllers/Auth/` controllers
- **Middleware**: Custom middleware in `app/Http/Middleware/`
- **Views**: Authentication views in `resources/views/auth/`
- **Frontend**: Styles in `resources/css/`, scripts in `resources/js/`

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).