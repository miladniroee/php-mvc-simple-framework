<?php

namespace Core;


use PDO;
use PDOException;

class Model
{

    /**
     * @return PDO|null
     */
    public function Connect(): ?PDO
    {
        $conn = NULL;
        try {
            //Install database
            $conn = new PDO("mysql:host=" . config('host') . ";charset=utf8", config('user'), config('pass'));
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "CREATE DATABASE IF NOT EXISTS " . config('db_name');
            // use exec() because no results are returned
            $conn->exec($sql);
            $conn->exec("USE " . config('db_name'));
        } catch (PDOException $e) {
            dd($e->getMessage());
        }
        return $conn;
    }

    /**
     * @param $conn
     */
    private function disconnect($conn)
    {
        unset($conn);
    }

    /**
     * @param string $sql
     * @param array $value
     * @param false $LastID
     * @return bool|string
     */
    private function InsertRow(string $sql, array $value = [], bool $LastID = false)
    {
        $result = null;
        $conn = $this->Connect();
        //Insert the Data in Database
        $stmt = $conn->prepare($sql);
        if (!count($value) == 0 and !$value == NULL):
            foreach ($value as $Key => $Value):
                $stmt->bindValue($Key + 1, $Value);
            endforeach;
        endif;
        if ($stmt->execute()):
            $result = true;
            if ($LastID) :
                $result = $conn->lastInsertId();
            endif;
        else:
            $result = false;
            $this->disconnect($conn);
        endif;
        return $result;
    }

    /**
     * @param string $sql
     * @param array $value
     * @param false $fetch
     * @return mixed
     */
    public function SelectRow(string $sql, array $value = [], bool $fetch = false)
    {
        $result = NULL;
        $conn = $this->Connect();
        //Select the Data in Database
        $stmt = $conn->prepare($sql);
        if (!count($value) == 0 and !$value == NULL) :
            foreach ($value as $key => $Value) :
                $stmt->bindValue($key + 1, $Value);
            endforeach;
        endif;
        if ($stmt->execute()) :
            if ($fetch) :
                $result = $stmt->fetch();
            else :
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            endif;
        else :
            $result = false;
            $this->disconnect($conn);
        endif;
        return $result;
    }

    /**
     * @param string $sql
     * @param array $value
     * @return bool
     */
    public function UpdateRow(string $sql, array $value): bool
    {
        $conn = $this->Connect();
        //Update the Data in Database
        $stmt = $conn->prepare($sql);
        if (!count($value) == 0 and !$value == NULL):
            foreach ($value as $Key => $Value):
                $stmt->bindValue($Key + 1, $Value);
            endforeach;
        endif;

        if ($stmt->execute()) :
            $result = true;
        else :
            $result = false;
            $this->disconnect($conn);
        endif;
        return $result;
    }


    public function DeleteRow(string $sql, array $value = []): bool
    {
        $conn = $this->Connect();
        //Update the Data in Database
        $stmt = $conn->prepare($sql);
        if (!count($value) == 0 and !$value == NULL):
            foreach ($value as $Key => $Value):
                $stmt->bindValue($Key + 1, $Value);
            endforeach;
        endif;

        if ($stmt->execute()) :
            $result = true;
        else :
            $result = false;
            $this->disconnect($conn);
        endif;
        return $result;
    }

}