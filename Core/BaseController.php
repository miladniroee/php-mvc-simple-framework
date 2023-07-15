<?php

namespace Core;

class BaseController
{
    protected $Database;
    protected string $Model;

    public function __construct()
    {
        if (isset($this->Model)) {
            $Model = 'Models\\' . $this->Model;
        } else {
            $Controller = explode('\\', static::class);
            $Model = 'Models\\' . end($Controller);
        }

        if (file_exists(str_replace('\\',DIRECTORY_SEPARATOR, $Model) . ".php")) {
            $this->Database = new $Model();
        } else {
            throw new Exception('There is problem with your model files');
        }
    }
}