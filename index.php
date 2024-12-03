<?php

date_default_timezone_set('Asia/Tehran');

session_start();

require_once(__DIR__ . '/Core/functions.php');

spl_autoload_register(function ($Name) {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $Name) . '.php');
});

if (php_sapi_name() !== 'cli'):
$Router = new Core\Router();
$Router->GetRoute();
endif;