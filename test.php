<?php
/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 27.12.2017
 * Time: 19:55
 */
$date = new DateTime();
$date->modify('-2 day');
$test = file_get_contents('https://cloud.roistat.com/api/v1/project/proxy-leads?project=54554&key=8cd2376c5525d59e82b3eb9df6e721ba&period=' . $date->format('Y-m-d') . '-' . date('Y-m-d'));
$test2 = json_decode($test, true);
$numkeys = array_reverse($test2['ProxyLeads']);
foreach ($numkeys as &$a) {
    $date = new DateTime($a['creation_date']);
    $date->modify('+3 hours');
    $a['creation_date'] =     $date->format('Y-m-d H:i:s');
}
header('Content-Type: application/json');
echo json_encode($numkeys);