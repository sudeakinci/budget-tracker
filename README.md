# Money Transfer Backend

A Laravel-based money transfer management system that supports user-to-user transfers, balance tracking, and automatic transaction recording via SMS integration.

## Features

- 💰 **Money Transfer Management**: Record and track user-to-user money transfers
- 👤 **User Management**: Registration, authentication, and profile management
- 🏦 **Payment Methods**: Define banks and payment systems
- 📱 **SMS Integration**: Automatic transaction creation from bank SMS messages
- 💳 **Balance Tracking**: Automatic balance calculation and validation
- 🔐 **Security**: Sanctum token-based API authentication
- 📊 **Dashboard**: Web interface for transaction tracking

## Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite (development) or MySQL/PostgreSQL (production)

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd money-transfer-backend
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

#### Email Configuration (SMTP)
Update the following variables in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password  # Use App Password for Gmail
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_ENCRYPTION=tls
```

**For Gmail:**
1. Enable 2-Factor Authentication
2. Generate App Password at: https://myaccount.google.com/apppasswords
3. Use the App Password instead of your regular password

### 4. Database Setup

#### SQLite (Recommended for Development)
```bash
touch database/database.sqlite
php artisan migrate
```

#### MySQL (Alternative)
Update `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=money_transfer
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then run:
```bash
php artisan migrate
```

### 5. Start the Application
```bash
composer run dev
```

This command starts concurrently:
- Laravel server: http://127.0.0.1:8000
- Vite dev server: http://localhost:5173
- Queue worker

## Usage

### Web Interface
Open `http://127.0.0.1:8000` in your browser:
- Register or login
- View transactions on dashboard
- Add new money transfers
- Manage payment methods

### API Endpoints

#### Authentication
```bash
POST /api/register    # Register new user
POST /api/login       # Login and get token
POST /api/logout      # Logout (requires token)
```

#### Transactions
```bash
GET    /api/transactions      # List transactions
POST   /api/transactions      # Create new transaction
DELETE /api/transactions/{id} # Delete transaction
```

#### Payment Terms
```bash
GET    /api/payment-terms      # List payment methods
POST   /api/payment-terms      # Add new payment method
PUT    /api/payment-terms/{id} # Update payment method
DELETE /api/payment-terms/{id} # Delete payment method
```

#### SMS Integration
```bash
POST /api/sms-integration/{smsSettingId} # Process SMS message
```

### SMS Integration Example

Currently supports Ziraat Bank SMS format:
```
# Incoming Transfer
"25/07 09:45 HESABINIZA JOHN DOE TARAFINDAN 500.00 TL GONDERILDI"

# Outgoing Transfer  
"25/07 14:30 HESABINIZDAN JANE DOE HESABINA 250.00 TL GONDERILDI"
```

## Commands

### Development
```bash
composer run dev        # Start all services
php artisan serve      # Laravel server only
npm run dev           # Vite only
php artisan queue:listen  # Queue worker only
```

### Testing
```bash
composer test          # Run tests
php artisan test      # PHPUnit tests
```

### Production Build
```bash
npm run build         # Compile frontend assets
php artisan optimize  # Laravel optimization
```

### Database
```bash
php artisan migrate           # Run migrations
php artisan migrate:rollback  # Rollback last migration
php artisan migrate:fresh     # Reset database
php artisan tinker           # Interactive shell
```

## Project Structure

```
money-transfer-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/        # API controllers
│   │   │   └── Web/        # Web controllers
│   └── Models/             # Eloquent models
├── database/
│   ├── migrations/         # Database migrations
│   └── database.sqlite     # SQLite database
├── resources/
│   └── views/             # Blade templates
├── routes/
│   ├── api.php           # API routes
│   └── web.php           # Web routes
└── tests/                # Test files
```

## Key Features

### Balance Management
- Automatic balance update on transaction creation
- Insufficient balance validation for outgoing transfers
- Balance restoration on transaction deletion

### Transaction Types
- **Manual Transactions**: Added by users
- **SMS Transactions**: Auto-created from SMS (`is_sms = true`)
- **Included/Excluded**: Transactions can be marked as included/excluded (`is_included`)

### Payment Methods
- **Global**: Visible to all users (`created_by = null`)
- **User-specific**: Only visible to creator

## Security

- Laravel Sanctum for API token authentication
- Session-based web authentication
- Rate limiting (3-5 attempts per minute)
- Account unlock via email verification
- Soft delete for user accounts

## API Documentation

API documentation is available via L5-Swagger package. After starting the application, visit:
```
http://127.0.0.1:8000/api/documentation
```

## Contributing

Please follow these steps to contribute:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License.