# laravel-set-autoincrement
Wanted to easily set the starting ID for your models? This package makes it easy as a breeze!

# Installation

Run the below command in your Laravel project root.
```bash
composer require sausin/laravel-set-autoincrement
```
This will install the latest version of the package for you.

For Laravel versions `5.5` and above, the service provider will automatically register and do the needful for you. For Laravel `5.4`, you will have to register the service provide in `config/app.php` under `'providers'` key. Add the below entry:
```php
Sausin\DBSetAutoIncrement\SetAutoIncrementProvider::class,
```

# Configuration

The default configuration will set the package such that except a few tables, the `AUTO_INCREMENT` counter will be set at `100001` (one hundred thousand and one).

If you would like to change the starting value and the tables affected by it, you can publish the configuration using
```bash
php artisan vendor:publish
```
and then select the option which corresponds to `Sausin\DBSetAutoIncrement\SetAutoIncrementProvider`. This will add a config named `auto-increment.php` in your project's config folder.

Change the values as you need.

# Behavior

It will run every time you're finished running migrations, whether for the first time or incremental. All tables where the `AUTO_INCREMENT` value is higher than the value in the config will be left alone. Changes will be made in all the rest.

# Supported SQL Drivers

Currently `mysql` and `sqlite` are supported. Plan to have support for more drivers in future. 
