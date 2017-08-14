[![StyleCI](https://styleci.io/repos/32406904/shield)](https://styleci.io/repos/32406904)
[![Build Status](https://travis-ci.org/Cyvelnet/laravel5-fractal.svg?branch=master)](https://travis-ci.org/Cyvelnet/laravel5-fractal)
[![Total Downloads](https://poser.pugx.org/cyvelnet/laravel5-fractal/downloads)](https://packagist.org/packages/cyvelnet/laravel5-fractal)
[![Latest Stable Version](https://poser.pugx.org/cyvelnet/laravel5-fractal/v/stable)](https://packagist.org/packages/cyvelnet/laravel5-fractal)
[![Latest Unstable Version](https://poser.pugx.org/cyvelnet/laravel5-fractal/v/unstable)](https://packagist.org/packages/cyvelnet/laravel5-fractal)
[![License](https://poser.pugx.org/cyvelnet/laravel5-fractal/license)](https://packagist.org/packages/cyvelnet/laravel5-fractal)

# laravel5-fractal
A simple fractal service provider and transformer generator for laravel 5 and lumen

* [Installation](#installation)
* [Config](#config)
* [Command](#command)
* [Usage](#usage)
* [Trait](#trait) (Optional feature since >= 2.1.3)

## Installation

#### Laravel
Require this package with composer using the following command:
````bash 
composer require cyvelnet/laravel5-fractal
````
After updating composer, add the ServiceProvider to the providers array in config/app.php 
````php
Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider::class,
````

and register Facade
And optionally add a new line to the `aliases` array:

'Fractal' => Cyvelnet\Laravel5Fractal\Facades\Fractal::class

#### Lumen
register service provider in /bootstrap/app.php for lumen
    
````php    
$app->register(Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider::class);
````

and uncomment the line

````php
$app->withFacades();
````

and finally register Facade with

````php
class_alias(Cyvelnet\Laravel5Fractal\Facades\Fractal::class, 'Fractal');
````

### Config
You can also publish the config file to change implementations to suits you.

````bash
php artisan vendor:publish --provider="Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider"
````   

##### Automatic sub resources injection.

Auto inject/embed sub resources are disabled by default, to enable this feature, edit ``config/fractal.php`` and set

``autoload => true``


### Command
`cyvelnet/fractal` come with a helpful commandline to assist your api transformation, just type and your Eloquent model attributes will be added to your transform array automatically
````bash
 // generate a empty transformer
 php artisan make:transformer UserTransformer
 
 // generate a modeled transformer
 php artisan make:transformer UserTransformer -m User
````

### Usage

### Fractal::item();
Transform a single record
```php 

$user = User::find(1);

Fractal::item($user, new UserTransformer());

```

### Fractal::collection();
Transform a collection of records
```php 

$users = User::where('activated', true)->get();

// $resourceKey is optional for most serializer, but recommended to set for JsonApiSerializer
$resourceKey = 'user';

Fractal::collection($users, new UserTransformer(), $resourceKey);

```

### Fractal::includes()
Inject sub resources
```php 

Fractal::includes('orders')  // where 'orders' is defined in your transformer class's $availableIncludes array

```

### Fractal::excludes()
Remove sub resources
```php 

Fractal::excludes('orders')

```

### Fractal::setSerializer()
Change transformer serializer
```php 

Fractal::setSerializer(\Acme\MySerializer); // where MySerializer is a class extends \League\Fractal\Serializer\SerializerAbstract 

```

### Fractal::fieldsets()
add sparse fieldset
```php 

Fractal::fieldsets(['orders' => 'item,qty,total,date_order'])
```

### Fractal::addMeta()
add extra meta data to root
```php 

// specify with single meta data
Fractal::addMeta($key = 'metaKey', $data = 'metaData')

// add an array of meta data
Fractal::addMeta([
    'key1' => 'data1',
    'key2' => 'data2'
    ])

```

## Trait

https://github.com/Cyvelnet/laravel5-fractal/wiki/Transformable-Trait
