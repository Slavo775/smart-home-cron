<?php
/**
 * Created by PhpStorm.
 * User: slavomir.sedlak
 * Date: 2019-08-11
 * Time: 18:30
 */

include 'cron_base.php';
require_once '../vendor/autoload.php';

class run extends cron_base
{
    public function getDevices()
    {
        $results = $this->executeQuery('SELECT ip, id_device FROM device WHERE active=1');
        foreach ($results as $result) {
            try {
                $ctx = stream_context_create(array(
                    'http' =>
                        array(
                            'timeout' => 5,  //60 Seconds is 1 Minutes
                        )
                ));
                $json = file_get_contents('http://' . $result['ip'] . ':8080/deviceStatus', false, $ctx);
                $checkErrors = $this->executeQuery('SELECT id_device FROM status_log WHERE id_device = :id_device AND resolved = 0 AND status_code = 1 LIMIT 1', ['id_device' => $result['id_device']]);
                if (!empty($json)) {
                    $status = json_decode($json);
                    if (!empty($status)) {
                        if(!empty($checkErrors)){
                            $updateField = $this->executeQuery('UPDATE status_log SET status_type = 2, status_code = 2 WHERE id_device = :id_device AND resolved = 0', ['id_device' => $result['id_device']]);
                        }
                        continue;
                    }
                }
                if(!empty($checkErrors)){
                    continue;
                }
                $params = [
                    'status_type' => 1,
                    'id_device' => $result['id_device'],
                    'status_code' => 1
                ];
                $insert = $this->executeQuery('INSERT INTO status_log (status_type, status_time, id_device, status_code) VALUES (:status_type, NOW(), :id_device, :status_code)',$params);
            } catch (Exception $ex) {
                $this->executeQuery('INSERT INTO status_log (status_type, status_time, id_device, message, status_code) VALUES (:status_type, NOW(), :id_device, :status_code)',
                    [
                        'status_type' => 1,
                        'id_device' => $result['id_device'],
                        'status_code' => 3
                    ]);
            }

        }
        return true;
    }
}

$run = new run();
$run->getDevices();
