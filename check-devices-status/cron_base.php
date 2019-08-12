<?php
/**
 * Created by PhpStorm.
 * User: slavomir.sedlak
 * Date: 2019-08-11
 * Time: 19:59
 */


class cron_base
{
    /**
     * @var PDO
     */
    private $conn;

    public function __construct()
    {
        $this->conn = new \PDO('mysql:host=127.0.0.1;port=8889;dbname=smart-home',
            'root',
            'root',
            array(\PDO::ATTR_PERSISTENT => true));
    }

    public function executeQuery(string $query, array $params = []){
        $statement = $this->conn->prepare($query);
        $statement->execute($params);
        $arr = $statement->errorInfo();
        $result = $statement->fetchAll();
        return $result;
    }

}
