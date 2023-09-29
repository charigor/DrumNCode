## Install project steps
- composer install
- cp .env.example .env
- ./vendor/bin/sail up -d
- ./vendor/bin/sail artisan key:generate
- ./vendor/bin/sail artisan migrate  
  or with seed ./vendor/bin/sail artisan migrate --seed

## Ports

**Must be available** MYSQL PORT 3307; SERVER PORT: 80

## POSTMAN COLLECTION
https://api.postman.com/collections/9442383-5ca7ed50-50b0-4d43-8c78-448214aab7ad?access_key=PMAT-01HBGB0JG3SC01KQDQ7QTC0TG1
