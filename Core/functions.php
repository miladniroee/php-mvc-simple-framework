<?php


function dd($v)
{
    var_dump($v);
    exit();
}


function view($Name, $Data = [])
{
    extract($Data);
    if (file_exists(__DIR__ . "/../Views/$Name.php")):
        require_once __DIR__ . "/../Views/$Name.php";
    else:
        echo "<h1 style='color: red;text-align: center'>View [$Name] Not found</h1>";
        exit();
    endif;
}

function config($Key)
{
    $Config = require __DIR__ . '/config.php';

    if (array_key_exists($Key, $Config)):
        return $Config[$Key];
    else:
        echo "<h1 style='color: red;text-align: center'>[$Key] not found in config.php file</h1>";
        exit();
    endif;
}


function route($RouteName, $Data = []): string
{
    return config('app_url') . (new Core\Router)->GetRouteByName($RouteName, $Data);
}

function redirect($RouteName, $Data = [])
{
    header('Location: ' . route($RouteName, $Data));
    exit();
}

function public_dir(string $File): string
{
    if (strpos($File, '/') === 0):
        $File = substr($File, 1);
    endif;

    return config('public_url') . $File;
}

function abort($Code = 404)
{
    http_response_code($Code);

    if (file_exists(__DIR__ . "/../Views/errors/$Code.php")) {
        view("errors/$Code");
    } else {
        echo "Error $Code";
    }

    exit();
}

