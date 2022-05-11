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
- A PIN should not be repeated until all preceding valid PINs have been emitted - This is ensured by making use of Laravel caching. A `count` of valid PINs that have been generated (and also each valid PIN) are stored in the cache indefinitely. 
Whenever a new PIN is to be generated, the `count` is compared with the total number of valid PINs (`$validPins = $possiblePins - $numberOfObviousPins`), if less than or equal (meaning we have run out of valid PINs), then the cache is cleared.
Otherwise, the cache is checked for the newly generated PIN, if it already exists on the cache, a new PIN is generated.

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


## Usage

Easily generate PINs by calling the `generatePin()` method on the `Generator` facade:
```php
use Faaizz\PinGenerator\Facades\Generator;
...
$pin = Generator::generatePin();
```


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

## Testing
Run tests by executing:
```bash
# Note: composer install must have been run to install dependencies
./execTests.sh
```

## References
- [Cryptographically-secure pseudorandom number generator - Wikipedia](https://en.wikipedia.org/wiki/Cryptographically-secure_pseudorandom_number_generator)
