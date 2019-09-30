# Laravel Eloquent Repositories

> Heads up! This is not yet another base class where there are some methods trying to act as Eloquent replacement.
This is an implementation where you can use all Eloquent features on a custom class!

Using Repositories in Laravel can be a bit confusing. If you create custom classes functioning as repositories
you can't really use Eloquent anymore, which is one of the best features of Laravel. That's why I was looking for another
way for using the repository pattern in Laravel. I came up with this approach and thought I would share it.

## Installation

Just install it through Composer:

```
composer require mratiebatie/laravel-repositories
```

After installation you can start using the repository pattern with Laravel.

## Example

In this example I assume that you already have a model named Product.

```php
<?php

namespace App\Repositories;

use MrAtiebatie\Repository;
use Illuminate\Database\Eloquent\Model;

/**
 * Product repository
 */
class ProductRepository extends Model
{
    use Repository;

    /**
     * The model being queried.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = app(\App\Models\Product::class);
    }

    /**
     * Find published products by SKU
     * @param  {int} $sku
     * @return {Product}
     */
    public function findBySku(int $sku): Product {
        return $this->whereIsPublished(1)
                   ->whereSku($sku)
                   ->first();
    }
}
```

There is one required property for using repositories. This is the `$model` property.
Here you define which Model, Eloquent should query when you call Eloquent methods on the repository.
Eloquent will automatically grab the `$table` property from the model, so you don't have to worry about that.

The suggested way to initialize the `$model` property is by using the IoC container.
This way you can always replace models for Mock objects when making unit tests.

Now you can use the Eloquent methods on the repository, like you would use them on an Eloquent model.

```php
<?php

/**
 * In your routes/web.php
 */

$router->get('/', function (\App\Repositories\ProductRepository $productRepo) {

    // Use any Eloquent feature directly
    $productRepo->all()->dd();

    // Use your custom repository methods
    echo $productRepo->findBySku(12345)->name;

    // You can even query relations
    echo $productRepo->first()->category;

});
```

I keep the following as a rule of thumb:

 - When you're chaining more than 2 Eloquent methods, make a Repository method for it. This goes for all kind of methods, relationships, query scopes etc.

## Credits

Sjors van Dongen (sitesjors@hotmail.com)
