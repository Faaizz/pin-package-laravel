# PIN Generator Package for Laravel

A Laravel Package to generate cryptographically-secure pseudorandom PINs (personal identification numbers).

Target Features:
- Each PIN comprises four numeric digits (e.g. "2845")
- "Obvious" numbers should not be allowed (e.g. "1111", "1234")
- PINs should be generated in apparently random order
- A PIN should not be repeated until all preceding valid PINs have been emitted - even if the program is restarted between PINs.

Achieved Features:
- Each PIN comprises four numeric digits (e.g. "2845"): this is fulfilled by ensuring that each generated PIN is formatted as a 4-digit string.
- "Obvious" numbers should not be allowed (e.g. "1111", "1234"): this is satisfied by checking generated PINs against a pre-specified list of obvious numbers.
- PINs should be generated in apparently random order: this is satisfied by the usage of PHP's `random_int()` function (which generates cryptographically secure pseudo-random integers).
- A PIN should not be repeated until all preceding valid PINs have been emitted - even if the program is restarted between PINs: Naturally, the probability of randomly generating the same 4-digit number (for example) in succession is `1` out of `100,000,000`. Using a pseudorandom generator increases this probability, however, the chances of repeating a PIN are still very small. Thus, no computation power needs to be wasted in fulfilling this feature.

## Installation

To install PIN Generator package, run:
```shell
composer require faaizz/pin_generator
```

### Autoloading
To automatically register the package with a Laravel project, the `PinGeneratorServiceProvider` can be added under `extra->laravel->providers` in `composer.json` as shown below:
```json
...
"extra": {
    "laravel" : {
        "providers": [
            ...
            "Faaizz\\PinGenerator\\PinGeneratorServiceProvider"
            ...
        ]
    }
}
```
The service provider can also be manually registered in the Laravel project's `app/config/app.php` as:
```php
<?php
...
'providers' => [
    ...
    Faaizz\PinGenerator\PinGeneratorServiceProvider::class
],
...
```

### Facade
The `Generator` facade can be used from the `Faaizz\PinGenerator\Facades` namespace after the `PinGeneratorServiceProvider` has been registered with a Laravel project.


## Configuration

### Publish
To publish package configs into a Laravel project, run:
```shell
php artisan vendor:publish --provider="Faaizz\PinGenerator\PinGeneratorServiceProvider" --tag="config"
```
This publishes the config into `app/config/pingenerator.php`.

### Customization
To configure the package, edit `app/config/pingenerator.php` in Laravel project appropriately.

- **Change number of digits:** To change the number of digits of generated PINs, map the desired value to the `digit` key. For example, to generate 10-digit PINs:
```php
// config/config.php
return [
    'digits' => 10
    ...
];
```

- **Add/Remove Obvious Number:** To add/remove "obvious" numbers, edit the mapped value of `obvious_numbers` as required:
```php
// config/config.php
return [
    ...
    'obvious_numbers' => [
        0000, 1111, 2222, 3333, 4444, 5555, 6666, 7777,
        8888, 9999, 1234, 5678, 1010, 2020
    ]
];
```

## Usage

Easily generate PINs by calling the `generatePin()` method on the `Generator` facade:
```php
use Faaizz\PinGenerator\Facades\Generator;
...
$pin = Generator::generatePin();
```

## References
- [Cryptographically-secure pseudorandom number generator - Wikipedia](https://en.wikipedia.org/wiki/Cryptographically-secure_pseudorandom_number_generator)
