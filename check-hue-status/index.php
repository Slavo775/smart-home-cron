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
        $q = 'SELECT * FROM device WHERE active = 1 AND device_group = 2';
        $hue_devices = $this->executeQuery($q);
        foreach ($hue_devices as $device) {
            $ip = $device['ip'];
            $id = $device['id_device'];
            $hueContent = file_get_contents('http://192.168.31.36/api/AH7Or1g7rXJhJbOwv1VEDA-kPLra6O-JAu3waKqk/lights/' . $ip);
            $hueJson = json_decode($hueContent);

            $q = 'SELECT * FROM status_log WHERE id_device = :id_device AND resolved = 0 AND status_type = 1 LIMIT 1';
            $bind = ['id_device' => $id];
            $result = $this->executeQuery($q, $bind);

            if ($hueJson->state->reachable && !empty($result)) {
                $q = 'UPDATE status_log SET status_type = 2, status_code = 2 WHERE id_device = :id_device AND resolved = 0';
                $update = $this->executeQuery($q, $bind);
            }
            if (!$hueJson->state->reachable && empty($result)) {
                $q = 'INSERT INTO status_log (status_type, status_time, id_device, status_code) VALUES (:status_type, NOW(), :id_device, :status_code)';
                $params = [
                    'status_type' => 1,
                    'id_device' => $id,
                    'status_code' => 1
                ];
                $insert = $this->executeQuery($q, $params);
            }
        }
    }
}

$run = new run();
$run->getHueDevice();
