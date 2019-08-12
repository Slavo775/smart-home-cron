<?php
/**
 * Created by PhpStorm.
 * User: slavomir.sedlak
 * Date: 2019-08-11
 * Time: 18:30
 */

include 'cron_base.php';


class run extends cron_base {

    public function getDevices(){
       $result =  $this->executeQuery('SELECT ip FROM device');
       echo var_export($result);
    }
}

$run = new run();
$run->getDevices();
