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

    public function __construct($domain, $user_id, $webhook)
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
    static function sendRequest($url, $request = null)
    {
        $url = 'https://' . self::$domain . '/rest/' . self::$user_id . '/' . self::$webhook . "/$url";//TODO подумать над http и https

        $queryData = http_build_query($request);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }

    /**
     * @param string $class_name
     * @param null|array $filter
     * @return Base[]
     */
    public function getList($class_name, $filter = null)
    {
        $url = "crm.$class_name.list";
        if (!empty($filter)) {
            $request = array('filter' => $filter);
            $getRaw = bitrix::sendRequest($url, $request);
        } else {
            $getRaw = bitrix::sendRequest($url);
        }
        $group_of_obj = array();
        foreach ($getRaw['result'] as $item) {
            /** @var Base $obj_list */
            $bit = "\\Bitrix\\$class_name";
            $obj_list = new $bit($item['ID']);
            $group_of_obj[] = $obj_list;
        }
        return $group_of_obj;
    }

    /**
     * функция логирования
     * @param $var
     */
    public function getLeadList($filter = null)
    {
        return $this->getList('lead', $filter);
    }

    public function searchLeads($phone, $email = null)
    {
        $leads = array();
        if (!empty($phone)) {
            $filter = array("PHONE" => $phone);
            $result = $this->getLeadList($filter);
            foreach ($result as $lead) {
                $leads[$lead->getId()] = $lead;
            }
        }
        if (!empty($email)) {
            $filter = array("EMAIL" => $email);
            $result = $this->getLeadList($filter);
            foreach ($result as $lead) {
                $leads[$lead->getId()] = $lead;
            }
        }
        return $leads;
    }


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