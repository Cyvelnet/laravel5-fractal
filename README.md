# laravel5-fractal
A simple fractal service provider and transformer generator for laravel 5

After updating composer, add the ServiceProvider to the providers array in config/app.php

   
    'Cyvelnet\Laravel5Fractal\Laravel5FractalServiceProvider',
    
Now you can start using this package by the following simple command

    
    $user = User::find(1);
    
    Fractal::item($user, new UserTransformer);
    
OR
 
    $users = User::get(); // $users = User::paginate();
    
    Fractal::collection($users, new UserTransformer);
    
You will automatically gain some extra attributes when you passing a laravel's paginator object.


what is leaving is create the file UserTransformer

You may now generate transformer classes in artisan

 
    php artisan make:transformer
    
in this case we are going to use artisan make:transformer UserTransformer, file will automatically created in App\Transfomers

now you may open your Transformer file

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
    
    
