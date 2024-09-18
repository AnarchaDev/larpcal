<?php

declare(strict_types=1);

namespace Nahkampf\Larpcal;

class DB
{
    public $conn;

    public function __construct()
    {
        try {
            $this->conn = new \PDO(
                $_ENV["DB_DRIVER"] . ":"
                . "host=" . $_ENV["DB_HOST"] . ";"
                . "dbname=" . $_ENV["DB_DBNAME"] . ";"
                . "port=" . $_ENV["DB_PORT"] . ";",
                $_ENV["DB_USER"],
                $_ENV["DB_PASSWORD"]
            );
        } catch (\PDOException $e) {
            trigger_error($e->getMessage());
        }
    }

    public function query($sql)
    {
        return $this->conn->query($sql);
    }

    /**
     * Quick and dirty SQL Inject protection
     */
    public function e($str)
    {
        return $this->conn->quote($str);
    }
}
