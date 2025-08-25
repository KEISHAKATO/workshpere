# WORKSPHERE

Laravel: ^11  
PHP: 8.2+  
DB: MySQL 8

## Setup
cp .env.example .env
php artisan key:generate
# set DB creds in .env
php artisan migrate
npm install && npm run dev
php artisan serve

## Branching
main: release
develop: integration
feature/*: per feature

## Today
- Breeze auth installed
- DB connected and migrated
