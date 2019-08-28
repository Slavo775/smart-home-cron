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
        $hueContent = file_get_contents('http://192.168.31.36/api/AH7Or1g7rXJhJbOwv1VEDA-kPLra6O-JAu3waKqk/groups/');
        $hueJson = json_decode($hueContent);
        foreach ($hueJson as $key => $item) {
            $name = $item->name;
            $type = $item->type;
            $class = $item->class;
            $id_group = $key;
            $lights = $item->lights;
            $query = 'INSERT INTO group_hue_lights (id_group, type, class, name) VALUES (:id_group, :type, :class, :name)';
            $binds = ['id_group' => $id_group, 'type' => $type, 'class' => $class, 'name' => $name];
            $this->executeQuery($query, $binds);
            foreach ($lights as $light){
                $q = 'SELECT id_device FROM device WHERE ip = :id_hue LIMIT 1';
                $bind = ['id_hue' => $light];
                $id_device = $this->executeQuery($q, $bind);
                $q ='INSERT INTO hue_group (id_group, id_device) VALUES (:id_group, :id_device)';
                $bind = ['id_group' => $id_group, 'id_device' => $id_device[0]['id_device']];
                $this->executeQuery($q, $bind);
            }
        }
    }
}

$run = new run();
$run->getHueDevice();
