# laravel5-fractal
A simple fractal service provider and transformer generator for laravel 5 and lumen

<sup>welcome to my first laravel package, lets rock.<sup>

### New: Since version 1.2 auto class aliase has been removed.

Require this package with composer using the following command:

    composer require cyvelnet/laravel5-fractal

#### Laravel 5

After updating composer, add the ServiceProvider to the providers array in config/app.php for laravel>=5

    Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider::class,

and register Facade

    'Fractal' => Cyvelnet\Laravel5Fractal\Facades\Fractal::class

#### Lumen

register service provider in /bootstrap/app.php for lumen
    
    $app->register(Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider::class);

and uncomment the line

    $app->withFacades();

and finally register Facade with

    class_alias(Cyvelnet\Laravel5Fractal\Facades\Fractal::class, 'Fractal');



Now you can start using this package with the following simple command


    $user = User::find(1);

    return Fractal::item($user, new UserTransformer)->responseJson(200);

OR

    $users = User::get(); // $users = User::paginate();

    return Fractal::collection($users, new UserTransformer)->responseJson(200);


You will automatically gain some extra attributes when you passing a laravel's paginator object.

In case you would like to get only the transformed array, you may do

    Fractal::collection($user, new UserTransformer)->getArray();


You may now generate transformer classes in artisan


    php artisan make:transformer

in this case we are going to use artisan make:transformer UserTransformer, transformer file will automatically created in App\Transfomers\ directory

now you may open your generated transformer file and start formatting your data as you like

    public function transform($user)
    {
        return [
               'id' => $user->user_id,
               'name' => "{$user->user_firstname} {$user->user_lastname}",
               ...
               ];
    }


You can also publish the config-file to change implementations to suits you.


    php artisan vendor:publish --provider="Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider"
    
    
##### Auto embed sub resources.

Auto embed sub resouces are disabled by default, to enable this feature, edit ``config/fractal.php`` and set

``autoload => true``

or alternately you may add ``FRACTAL_AUTOLOAD=true`` to ``.env`` file.



###### TO DO
* add functionality to artisan command to generate sub transformer and includes function boilerplate
* allow mapping nested sub resouces to a much more meaningful and shorter identifier.

