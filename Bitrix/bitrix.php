<?php

/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 23.11.2017
 * Time: 20:04
 */
namespace Bitrix;

class bitrix
{
    public static $domain;
    public static $webhook;
    public static $user_id;

    public function __construct($domain,$user_id,$webhook)
    {
        self::$domain = $domain;
        self::$webhook = $webhook;
        self::$user_id = $user_id;
    }

    /**
     * Функция отправляет POST запрос, если параметр encode_json = null, то данные не будут конвертироваться в json
     * @param $url - url адрес куда нужно отправить запрос
     * @param $request - тело запроса
     * @return bool|string
     */
    static function sendRequest($url, $request=null)
    {
        $url = 'https://'.self::$domain.'/rest/'.self::$user_id.'/'.self::$webhook."/$url";
//            $result = file_get_contents($url,
//                false,
//                stream_context_create(
//                    array(
//                        'http' => array(
//                            'method' => 'POST',
//                            'header' => 'Content-type: application/json',
//                            'content' => json_encode($request)
//                        )
//                    )
//                )
//            );
//            return json_decode($result,true);
        $data_string = json_encode($request);

        $curl = curl_init("$url");

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    static function getList($class_name,$filter=null){
        $url = "crm.$class_name.list";
//        if (!empty($filter)) {
//            $request = array('filter'=>array('ID'=>$id));
//            return bitrix::sendRequest($url,$request);
//        }
            $group_of_obj = array();
            $test = bitrix::sendRequest($url,null);
            foreach ($test['result'] as $a) {
                /** @var deal|lead|contact $obj_list */
                $bit ="\\Bitrix\\$class_name";
                $obj_list = new $bit();
                $obj_list->loadInRaw($a);
                $group_of_obj[] = $obj_list;
            }
            return $group_of_obj;
    }
    /**
     * функция логирования
     * @param $var
     */
    function Logs($var)
    {
        $logfile = 'bitrix.log';
        $mode = 'a';
        if (!file_exists($logfile)) {
            $mode = 'w+';
        }
        $f = fopen($logfile, $mode);
        fwrite($f, PHP_EOL . "##########################################" . PHP_EOL . date('Y-m-d H:i:s') . ": " . print_r($var, 1));
    }
}