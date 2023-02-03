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

        if (file_exists($Model . ".php")) {
            $this->Database = new $Model();
        }
    }
}