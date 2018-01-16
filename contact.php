<?php

/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 20.12.2017
 * Time: 1:31
 */
namespace Bitrix;

require_once("bitrix.php");

class contact extends Base
{
    protected $bitrh_date;
    protected $contact_type;
    protected $post;
    public function setClassName()
    {
        $this->className = 'contact';
    }
    public function __construct($id=null)
    {
        $this->setClassName();
        if(!empty($id))
            $this->loadById($id);
    }

    public function loadInRaw($dataRaw)
    {
        parent::loadInRaw($dataRaw);
        $this->bitrh_date = $dataRaw['BIRTHDATE'];
        $this->contact_type = $dataRaw['TYPE_ID'];
        $this->post=$dataRaw['POST'];


    }
    public function getDealsByContact(){
//        return $deals = new deal()
    }

    /**
     * Функция поиска контакта по номеру телефона
     * @param $phone
     * @return mixed - возвращает массив
     */
    public function findContact($phone)
    {
        $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.contact.list";
        $request = '{"filter":{"PHONE":"' . $phone . '"}}';
        $result = json_decode($this->sendRequest($url, $request, null), true);
        return $result;
    }

    /**
     * Функция получает все сделки по контакту, параметрами могут быть id контакта и телефон
     * @param $id - контакта для поиска
     * @param $phone - телефон для фильтрации
     * @return mixed - возвращает массив
     */
    public function getDeals($id, $phone)
    {
        if ($id != null && $phone == null) {
            $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.contact.get?id=" . $id;
            $result = $this->sendRequest($url, null, null);
            return json_decode($result, true);
        }
        if ($id == null && $phone != null) {
            $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.contact.list";
            $request = '{"filter:{"PHONE":"$phone"}"}';
            $result = $this->sendRequest($url, $request, null);
            return json_decode($result, true);
        }
    }
}