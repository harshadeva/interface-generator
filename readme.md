
# **Interface Generator**

The `harshadeva/interface-generator` package simplifies the implementation of the Repository pattern in Laravel by automating the creation of interfaces, repositories, and service provider bindings. With a single Artisan command, you can generate custom repositories and interfaces, and ensure they are correctly bound in your application.

---

## **Features**
- **Automated Repository and Interface Creation**: Quickly generate repository and interface files using a simple Artisan command.
- **Dynamic Service Provider Binding**: Automatically binds repositories and interfaces to the `RepositoryServiceProvider`.
- **Smart Service Provider Management**: If the `RepositoryServiceProvider` does not exist, the package will create it and add it to the `config/app.php` providers array.

---

## **Installation**

Install the package via Composer:
```bash
composer require harshadeva/interface-generator
```

---

## **Usage**

Generate a repository and interface using the Artisan command:
```bash
php artisan make:interface CustomName
```

### What Happens:
1. The command generates:
   - `CustomNameRepositoryInterface`
   - `CustomNameRepository`

2. Binds the interface to its corresponding repository in the `RepositoryServiceProvider`.

3. If the `RepositoryServiceProvider` does not exist:
   - It will automatically create it.
   - Adds the `RepositoryServiceProvider` to the `config/app.php` providers array.

---

## **Example**

Run the following command:
```bash
php artisan make:interface User
```

This will create:
- `App\Repositories\UserRepositoryInterface`
- `App\Repositories\UserRepository`

Additionally, the package will ensure the following binding is added to your `RepositoryServiceProvider`:
```php
$this->app->bind(UserRepositoryInterface::class, UserRepository::class);
```

---

## **Customizing the Output**

If you want to customize the directory structure or namespace, you can modify the generated files after they are created.

---

## **Requirements**
- Laravel 8.x or higher
- PHP 7.4 or higher

---

## **Contributing**

Contributions are welcome! Please feel free to submit a pull request or open an issue if you encounter any problems or have suggestions for new features.

---

## **License**

This package is open-sourced software licensed under the [MIT license](LICENSE).
