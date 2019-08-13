<?php
/**
 * Created by PhpStorm.
 * User: slavomir.sedlak
 * Date: 2019-08-11
 * Time: 19:59
 */
namespace smart_home\cron;
require __DIR__.'vendor/autoload.php';


class cron_base
{
    /**
     * @var PDO
     */
    private $conn;

    public function __construct()
    {
        $this->conn = new \PDO('mysql:host=192.168.31.118;port=3306;dbname=smart-home',
            'root',
            'root',
            array(\PDO::ATTR_PERSISTENT => true));
    }

    public function executeQuery(string $query, array $params = []){
        $statement = $this->conn->prepare($query);
        foreach($params as $key => $param){
            $statement->bindParam(':'.$key, $param);
        }
        $statement->execute();
        $result = $statement->fetchAll(2);
        return $result;
    }

}
