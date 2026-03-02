# Laravel Repository Package

A Laravel package implementing a clean, scalable repository design pattern architecture to abstract data persistence and promote testable, maintainable code.
 
## Installation

### **Step 1: Install via Composer**  
Run the following command in your Laravel project:

```sh
composer require blaisebueno/laravel-repository
```

### **Step 2: Register the Service Provider for Publishing**  
If you are using Laravel 5.5+ with package auto-discovery, you can skip this step.  
Otherwise, you need to manually register the service provider.

### **For Laravel 10 and Below**  
Add the service provider in `config/app.php`:

### **For Laravel 11 and Above**  
Add the service provider in `bootstrap/providers.php`:

```php
\BlaiseBueno\LaravelRepository\LaravelRepositoryServiceProvider::class,
```

### **Step 3: Publish Files**  
Run the following command to publish the repository stubs:

```sh
php artisan vendor:publish --provider="BlaiseBueno\LaravelRepository\LaravelRepositoryServiceProvider"
```

This will publish the following:

- `app/Providers/RepositoryServiceProvider.php`
- `app/Console/Commands/MakeRepository.php`
- `app/Repositories/BaseRepository.php`
- `app/Repositories/Interfaces/EloquentInterface.php`

Adding `--force` flag will overwrite existing files.

### **Step 4: Register the Repository Service Provider**  

### **For Laravel 10 and Below**  
Add the service provider in `config/app.php`:

### **For Laravel 11 and Above**  
Add the service provider in `bootstrap/providers.php`:

```php
App\Providers\RepositoryServiceProvider::class,
```

### **Step 5: Register the `make:repository` Command**  

> ⚠️ In Laravel 11 and above, `Kernel.php` has been removed. Custom commands in `app/Console/Commands` are now auto-discovered.

> ⚠️ If your `App\Console\Kernel` contains the default line below, **no manual registration is required**:

```php
$this->load(__DIR__.'/Commands');
```

If you're using a custom path or want to register explicitly, add the following in `app/Console/Kernel.php`:
```php
protected $commands = [
    \App\Console\Commands\MakeRepository::class,
];
```

### **Step 6: Usage**  
You can now generate a new repository using:

```sh
php artisan make:repository User
```

This will create:
- `app/Repositories/UserRepository.php`
- `app/Repositories/Interfaces/UserInterface.php`

Adding `--force` flag will overwrite existing files.

If you want to generate a corresponding service file, add the `--s` flag:

This will additionally create:
- `app/Services/UserService.php`

Use `--s-only` flag if you only want to create the service file.

Modify these files according to your needs.

## License

This package is open-source and available under the [MIT license](LICENSE).
