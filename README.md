# Project Name

## Installation Instructions

Follow the steps below to set up the project on your local machine.

### Prerequisites

Make sure you have the following software installed:

- PHP >= 8.0
- Composer
- MySQL or any other supported database
- Laravel 10

### Clone the Repository

Clone the project repository to your local machine:

```bash
git clone https://github.com/riz081/master_bagasi.git
cd master_bagasi
composer install
cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

php artisan key:generate

php artisan:migrate

php artisan serve

```
