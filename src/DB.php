<?php

declare(strict_types=1);

namespace Nahkampf\Larpcal;

class DB
{
    public $conn;

    public function __construct()
    {
        // we are relying on a cloud mysql that might be sleeping,
        // so it might take 10 seconds or more for it to wake up.
        // therefore, try the connection x times with y sleep in between
        $maxTries = 3;
        $tries = 0;
        retry:
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
            if ($tries < $maxTries) {
                $tries++;
                sleep(3);
                goto retry;
            }
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

    public function getAll($sql)
    {
        return $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOne($sql)
    {
        return $this->query($sql)->fetch(\PDO::FETCH_ASSOC);
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }
}
