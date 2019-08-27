<?php
/**
 * Created by PhpStorm.
 * User: slavomir.sedlak
 * Date: 2019-08-11
 * Time: 18:30
 */

include 'cron_base.php';

class run extends cron_base
{
    public function getHueDevice()
    {
        $hueContent = file_get_contents('http://192.168.31.36/api/AH7Or1g7rXJhJbOwv1VEDA-kPLra6O-JAu3waKqk/lights/');
        $hueJson = json_decode($hueContent);
        foreach ($hueJson as $key => $item) {
            $description = $item->type;
            $name = $item->name;
            $mac = $item->uniqueid;
            $ip = $key;
            $type = $item->productname;
            $insert = $this->executeQuery('INSERT INTO device (name, ip, mac, description, type) VALUES (:name, :ip, :mac, :description, :type)',
                ['name' => $name, 'mac' => $mac, 'ip' => $ip, 'description' => $description, 'type' => $type]);
        }
    }
}

$run = new run();
$run->getHueDevice();
