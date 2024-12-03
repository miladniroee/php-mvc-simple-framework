<?php

use Core\Commands\Command;

if (php_sapi_name() !== 'cli') {
    exit("This script can only be run from the command line.\n");
}

require_once __DIR__ . '/index.php';

Command::cyan("\nWelcome to the PHP MVC framework CLI!\n\n");

$commands = [
    'migration:create',
    'migration:run',
    'migration:rollback',
    'migration:refresh',
];

echo "Available commands:\n";
foreach ($commands as $index => $command) {
    echo "  $index) ";
    Command::green($command);
    echo "\n";
}

echo "\n";

$command = readline("Enter a command: ");

switch ($command) {
    case 0:
        \Core\Commands\Migration::create();
        break;
    case 1:
        \Core\Commands\Migration::run();
        break;
    case 2:
        \Core\Commands\Migration::rollback();
        break;
    case 3:
        \Core\Commands\Migration::refresh();
        break;
    default:
        Command::red("Invalid command.");
        exit();
}


