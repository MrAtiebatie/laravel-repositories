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

## Make a repository class

This package provide a new Artisan command to create a repository class. All the classes will be generated on the `App\Repositories` folder, if this folder is missing, it will be generated automatically.

```
php artisan make:repository ProductRepository
```

You can use the `--model` option to define the Eloquent model that will be linked to this repository.

```
php artisan make:repository ProductRepository --model=App\\Product
```

Don't forget to write the **full namespace** of your model, using '\\\\' as the separator.

## Example

In this example I assume that you already have a model named Product.
Used the command 

`php artisan make:repository ProductRepository --model=App\\Models\\Product`


``` php
<?php

namespace App\Repositories;

use MrAtiebatie\Repository;
use App\Models\Product; 

class ProductRepository
{
    use Repository;

    /**
     * The model being queried.
     *
     * @var Model
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        // setup the model
        $this->model = app(Product::class);
    }
}
```
The magic appears with the `Repository` trait, and the `protected $model` property.
When you call an Eloquent method on your repository, this call will fallback to your model. <br>
So all the Eloquent methods like `where`, `all`, `find`, or your custom scopes are available in your repository.

The suggested way to initialize the `$model` property is by using the IoC container.
This way you can always replace models for Mock objects when making unit tests.

``` php
<?php

namespace App\Repositories;

use MrAtiebatie\Repository;
use App\Models\Product; 

class ProductRepository
{
    use Repository;

    /**
     * The model being queried.
     *
     * @var Model
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        // setup the model
        $this->model = app(Product::class);
    }

    /**
     * Find published products by SKU
     * 
     * @param  {int} $sku
     * 
     * @return {Product}
     */
    public function findBySku(int $sku): Product 
    {
        // using 'whereIsPublished' and 'whereSku' scopes
        // defined on the Product model
        return $this->whereIsPublished(1)
            ->whereSku($sku)
            ->first();
    }
}
```

```php
<?php

/**
 * In your routes/web.php
 */

Route::get('/', function (\App\Repositories\ProductRepository $productRepo) {

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

## Making facade

In our previous example, we used the dependency injection to retrieve our repository. <br>
If you want to use your repository without it like you are allowed to do it with a model, you need to create a Facade. <br>

- First, create your Facade, for example in `app/Facades/ProductRepository` :
``` php
<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ProductRepository';
    }
}

```

- Then, in your `app/Providers/AppServiceProvider.php`, register your Facade :
``` php
<?php

namespace App\Providers;

use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ProductRepository', function () {
            return new ProductRepository();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

```

- Final step, add this alias to your `config/app.php` file :
``` php
'ProductRepository' => App\Facades\ProductRepository::class,
```

Now, our `routes/web.php` example would be like this :

```php
<?php

/**
 * In your routes/web.php
 */

use App\Facades\ProductRepository;

Route::get('/', function () {

    // Use any Eloquent feature directly
    ProductRepository::all()->dd();

    // Use your custom repository methods
    echo ProductRepository::findBySku(12345)->name;

    // You can even query relations
    echo ProductRepository::first()->category;

});
```

## Credits

Sjors van Dongen (sitesjors@hotmail.com)
Yannick Leone (yannick.leone@gmail.com)
