# Laravel package to Set custom Auto Increment in database
[![Latest Version on Packagist](https://img.shields.io/packagist/v/sausin/laravel-set-autoincrement.svg?style=flat-square)](https://packagist.org/packages/sausin/laravel-set-autoincrement)
[![Total Downloads](https://img.shields.io/packagist/dt/sausin/laravel-set-autoincrement.svg?style=flat-square)](https://packagist.org/packages/sausin/laravel-set-autoincrement)
[![Quality Score](https://img.shields.io/scrutinizer/g/sausin/laravel-set-autoincrement.svg?style=flat-square)](https://scrutinizer-ci.com/g/sausin/laravel-set-autoincrement)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](https://opensource.org/licenses/MIT)

Wanted to easily set the starting ID for your models? This package makes it easy as a breeze! It's also possible to change auto increment on existing databases (i.e. new entries will start from the specified number).

# Installation

Run the below command in your Laravel project root.
```sh
composer require sausin/laravel-set-autoincrement
```
This will install the latest version of the package for you. The service provider will automatically register and make available the features of the package (which work automatically).

# Configuration

The default configuration will set the package such that except a few tables, the `AUTO_INCREMENT` counter will be set at `100001` (one hundred thousand and one).

To change the starting value and the tables affected by it, the configuration can be published using
```sh
php artisan vendor:publish
```
and then the option which corresponds to `Sausin\DBSetAutoIncrement\SetAutoIncrementProvider` needs to be selected. This will add a config named `auto-increment.php` in your project's config folder.

Change the values as desired.

# Usage

## Default Behavior

It will run every time  migrations are finished running, whether for the first time or incremental. All tables where the `AUTO_INCREMENT` value is higher than the value in the config will be left alone. Changes will be made in all the rest.

If, however, automatic updation behaviour is not desired, the `action` key in the config file can be changed to `manual` and the package will not take automatic action.

## Command line

Using the below command
```sh
php artisan db:set-auto-increment --tables=users --value=20001
```
the auto increment value can be changed manually. Multiple tables are accepted as input.

# Supported SQL Drivers

Currently `mysql` and `sqlite` are supported. Plan to have support for more drivers in future. 
