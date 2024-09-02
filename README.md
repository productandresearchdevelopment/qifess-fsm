# Asia Net  
Field Service Management (Asia Net)

## Installation

#### Clone Project

```bashd
https://gitlab.com/metanusa/asianet.git
```

####  Setup Environtment Variable (.env)
contoh salinan ada di file ".env.example", copy ".env"


```bash
Contoh Konfigurasi Database
----------------------------------

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dbname
DB_USERNAME=mysql-user
DB_PASSWORD=mysql-password

Contoh Konfigurasi Email
----------------------------------
MAIL_DRIVER=smtp
MAIL_HOST=mail.server.com
MAIL_PORT=465
MAIL_USERNAME=xxx@email.com
MAIL_PASSWORD=******
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=xxx@email.com
MAIL_FROM_NAME=xxxxx

```


#### Buat Directory
```bash
Under #/storage/framework
- sessions
- views
- cache

Under #storage/public
- uploads
```

#### Setup Composer
```cmd
composer install
```

#### Generate Key
```cmd
php artisan key:generate
```

#### Clear Cache
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear 
php artisan view:clear
```

#### Migrasi Database & Storage Link
```bash
php artisan migrate
php artisan storage:link
```

#### Default User & Password Web App
```bash
Username: admin
Password: admin
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

andika2000.blogspot.com
