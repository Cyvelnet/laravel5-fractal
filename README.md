# laravel5-fractal
A simple fractal service provider and transformer generator for laravel 5

<sup>welcome to my first laravel package, lets rock.<sup>

Require this package with composer using the following command:

    composer require cyvelnet/laravel5-fractal


After updating composer, add the ServiceProvider to the providers array in config/app.php for laravel>=5

    Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider::class,

OR register service provider in /bootstrap/app.php for lumen
    
    $app->register(Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider::class);

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

in this case we are going to use artisan make:transformer UserTransformer, transformer file will automatically created in App\Transfomers\ directory, alternately you may want to change the directory or namespace value, you may do so by providing command option

--directory="Directory Relative to App\" or
--namespace="Your Namespace"

to overwrite the default setting.

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


    php artisan vendor:publish

##### TO DO
* add functionality to artisan command to generate sub transformer and includes function boilerplate

