Allows you to seamlessly send data to a Statsd server from within your Laravel application.

[![Build Status](https://travis-ci.org/rcrowe/laravel-statsd.png?branch=master)](https://travis-ci.org/rcrowe/laravel-statsd)

Installation
============

Add `rcrowe\laravel-statsd` as a requirement to composer.json:

```javascript
{
    "require": {
        "rcrowe/laravel-statsd": "0.5.*"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Once Composer has installed or updated your packages you need to register Statsd with Laravel itself. Open up app/config/app.php and find the providers key towards the bottom and add:

```php
'rcrowe\Statsd\StatsdServiceProvider'
```

You will also need to register the facade so that you can access it within your application. To do this add the following to your aliases in app/config/app.php:

```php
'Statsd' => 'rcrowe\Statsd\Facades\Statsd'
```

Configuration
=============

Statsd configuration file can be extended by creating `app/config/packages/rcrowe/laravel-statsd/config.php`. You can find the default configuration file at vendor/rcrowe/laravel-statsd/src/config/config.php.

You can quickly publish a configuration file by running the following Artisan command.

```
$ php artisan config:publish rcrowe/laravel-statsd
```

Usage
=====

Laravel-Statsd exposes the following functions to send data to Statsd:

```php
Statsd::timing($key, $time);
```

```php
Statsd::gauge($key, $value);
```

```php
Statsd::set($key, $value);
```

```php
Statsd::increment($key);
```

```php
Statsd::decrement($key);
```

The data is automatically sent to Statsd at the end of Laravels life-cycle, but you can force data to be sent with:

```php
Statsd::send()
```

Note: Data will only be sent to Statsd if your environment matches the environments defined in the config file.
