<?php

namespace Models;

use Core\Model;

class Users extends Model
{
    public function getUsers()
    {
        $Query = "SELECT * FROM users WHERE `is_active` = 1";
        $this->SelectRow($Query);
    }
}