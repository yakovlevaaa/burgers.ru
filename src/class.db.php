<?php

class Db
{
    /** @var \PDO */
    private $pdo;
    private $log = [];
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function getConnection()
    {
        $host = DB_HOST;
        $db_name = DB_NAME;
        $db_user = DB_USER;
        $db_password = DB_PASSWORD;

        if (!$this->pdo) {
            $this->pdo = new \PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_password);
        }

        return $this->pdo;
    }

    public function fetchAll(string $query, $_method, array $params = [])
    {
        $t = microtime(true);
        $prepared = $this->getConnection()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $error_info = $prepared->errorInfo();
            return trigger_error("$error_info[0] " . "$error_info[1]: " . $error_info[2]);
        }

        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $affected_rows = $prepared->rowCount();
        $this->log[] = [$query, microtime(true) - $t, $_method, $affected_rows];

        return $data;
    }

    public function fetchOne(string $query, $_method, array $params = [])
    {
        $t = microtime(true);
        $prepared = $this->getConnection()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $error_info = $prepared->errorInfo();
            return trigger_error("$error_info[0] " . "$error_info[1]: " . $error_info[2]);
        }

        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $affected_rows = $prepared->rowCount();

        $this->log[] = [$query, microtime(true) - $t, $_method, $affected_rows];
        if (!$data) {
            return false;
        }
        return reset($data);
    }

    public function exec(string $query, $_method, array $params = []): int
    {
        $t = microtime(true);
        $pdo = $this->getConnection();
        $prepared = $this->getConnection()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $error_info = $prepared->errorInfo();
            return trigger_error("$error_info[0] " . "$error_info[1]: " . $error_info[2]);
        }

        $affected_rows = $prepared->rowCount();
        $this->log[] = [$query, microtime(1) - $t, $_method, $affected_rows];
        return $affected_rows;
    }

    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }

    public function getLogHTML()
    {
        if (!$this->log) {
            return '';
        }
        $res = '';
        foreach ($this->log as $elem) {
            $res = $elem[1] . ': ' . $elem[0] . ' (' . $elem[2] . ') [' . $elem[3] . ']' . PHP_EOL;
        }
        return '<pre>' . $res . '</pre>';
    }
}
