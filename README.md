# Simple PHP MVC Framework (no composer)

## Table of Content:

* [Requirement](#requirement)
* [Installation](#installation)
* [Usage](#usage)
* [Configuration](#configuration)
* [Routing](#routing)
* [Controller](#controller)
* [Database and Models](#database-and-models)
* [Helper functions](#helper-functions)
    * [view()](#view)
    * [redirect()](#redirect)
    * [public_dir()](#public_dir)
    * [abort()](#abort)
    * [dd()](#dd)

<br>

## Requirement

PHP 7.4+

## Installation

1- Download Zip and extract.

2- Run server:

```shell
php -S localhost:80
```

## Usage

### Configuration

* set database configuration on `Core/config.php`.
* access to configs using `config()` function

```php
echo config("db_name"); // php_mvc_framework
```

### Routing

you can define routes on `/Routes.php`;

* `url`  for access url.
* `name` for route name that can access with `route()` function.
* `controller` for controller class
* `method` for controller callback method

```php
[
    "url" => "/welcome",
    "name" => "welcome",
    "controller" => Controllers\Welcome::class,
    "method" => 'index'
]
```

<br>
<br>

#### Route Parameters

you can bind parameter to route using `{key}`:

```php
[
    "url" => "/user/{id}/show",
    "name" => "show_user",
    "controller" => Controllers\Users::class,
    "method" => 'show'
]
```

The `show()` function should accept a parameter with `$id` name as following:

```php
class Users extends BaseController
{
    public function show($id)
    {
        // code    
    }
}
```

<br>
<br>

#### Getting Route By name

you can get routes by its name:

```php
echo route('welcome'); // "localhost/welcome"
```

if your route has parameter you should define through an array:

```php
echo route('show_user', ['id' => 2]); // localhost/user/2/show
```

<br>

## Controller

You can make controllers within `/Controllers` directory.

Controllers should extend `Core/BaseController.php`

```php
namespace Controllers;

use Core\BaseController;

class Users extends BaseController
{
    // methods
}
```

<br>

## Database and Models

You can create model files within `/Models` directory. models should extend `Core/Model.php`:

```php
namespace Models;

use Core\Model;

class Users extends Model
{
    // methods
}
```

if you created your model as controller name, you do not need to define model in controller.

you can access it with `$this->Database` in controller:

```php
class Users extends BaseController
{
    public function show()
    {
        dd($this->Database->GetAllUsers());
    }
}
```

if you want to use another one you can define it in Controller:

```php

protected string $Model = "ShowUser";

```

there are four methods for CRUD that you have access in model.

how to select:

```php
 public function getUsers()
 {
    $Query = "SELECT * FROM users WHERE is_active = ? AND id != ?";
    $Data = [
        1,
        5
    ];
    return $this->InsertRow($Query,$Data);
 }
```
and other methods:
```php
$this->SelectRow($Query,$Data);
$this->UpdateRow($Query,$Data);
$this->DeleteRow($Query,$Data);
```


## Helper functions

#### view()

loads a view file from `View`:

```php
view('dashboard/profile'); // require View/dashboard/profile.php
```

you can pass data to view by `compact()` inner function.

```php
$Users = ['Milad','Ali']; // Data

view('dashboard/profile',compact('Users'));

// or

view('dashboard/profile',['users'=>$Users])
```

then you can access data in view:

```php
<?php foreach ($Users as $User): ?>
    <tr>
        <td><?=$User?></td>
    </tr>
<?php endforeach; ?>
```

<br>

#### redirect()

redirect to route by passing route name or url:

```php
redirect('index'); // redirects to index route

redirect('/dashboard/profile');
```

if route has parameter, you can pass it as in route function

```php
redirect('show_user', ['id' => 1 ]);
```

<br>

#### public_dir()

it returns string of file and public dir that defined in `config.php`:

```php
echo public_dir('style.css'); // http://localhost/public/style.css
```

<br>

#### abort()

this function abort execution with given status code.

you can define view for it in `Views/errors`.

<br>

#### dd()

it will die and dump what ever you give it.

```php
dd("hi"); // string(2) "hi"
```


# Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

For any issue or feature request, please open an issue.

# License

[MIT License](https://choosealicense.com/licenses/mit/)
