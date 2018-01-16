<?php

/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 01.12.2017
 * Time: 13:49
 */
namespace Bitrix;

require_once("bitrix.php");
require_once 'Base.php';
class lead extends Base
{
    public function setClassName()
    {
        $this->className = 'lead';
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
        $this->status = $dataRaw['STATUS_ID'];
    }
    /**
     * @param null $id
     * @param null $phone
     * @return mixed
     */
    public function getLead($id = null, $phone = null)
    {
        if ($id != null && $phone == null) {
            $request = file_get_contents("https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.lead.get?id=" . $id);
            return json_decode($request, true);
        }
        if ($phone != null && $id == null) {
            $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.lead.list";
            $phonenumber = (object)array(
                "filter" => (object)array(
                    "PHONE" => "$phone"
                )
            );
            $answer = $this->sendRequest($url, $phonenumber, true);
            return json_decode($answer, true);
        }
    }
    /**
     * @param $title
     * @param $status
     * @param null $f_name
     * @param null $l_name
     * @param null $phone
     * @param null $email
     * @param null $resp_id
     * @param string $type_sum
     * @param null $sum
     * @return mixed
     */
    public function addLead($title, $status, $f_name = null, $l_name = null, $phone = null, $email = null, $resp_id = null, $sum = null,$type_sum = "RUB")
    {
        $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.lead.add";
        $lead_data = (object)array(
            "fields" => (object)array(
                "TITLE" => "$title",
                "STAGE_ID" => "$status",
                "OPENED" => "Y",
                "ASSIGNED_BY_ID" => "$resp_id",
                "CURRENCY_ID" => "$type_sum",
                "OPPORTUNITY" => "$sum",
                "NAME" => $f_name,
                "LAST_NAME" => $l_name,
                "SECOND_NAME" => "-",
                "PHONE" => array(
                    (object)array(
                        "VALUE" => "$phone",
                        "VALUE_TYPE" => "MOBILE",
                        "TYPE_ID" => "PHONE"
                    )
                ),
                "EMAIL" => array(
                    (object)array(
                        "VALUE" => "$email",
                        "VALUE_TYPE" => "WORK",
                        "TYPE_ID" => "EMAIL"
                    )
                )
            )
        );
        $result = file_get_contents($url,
            false,
            stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/json',
                        'content' => json_encode($lead_data)
                    )
                )
            )
        );
        $created_lead_id = json_decode($result, true)['result'];
        return $created_lead_id;
    }

    /**
     * @param $lead_id
     * @param $title
     * @param $deadline
     * @param $resp_id
     */
    public function addTask($lead_id, $title, $deadline, $resp_id)
    {
        $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/task.item.add";
        $task_data = null;
        $task_data = (object)array(
            "arNewTaskData" => (object)array(
                "TITLE" => $title,
                "DEADLINE" => $deadline,
                "RESPONSIBLE_ID" => $resp_id,
                "UF_CRM_TASK" => "L_$lead_id"
            )
        );
        $this->sendRequest($url,$task_data,"yes");
    }

    /**
     * @param $lead_id
     * @param null $roi_value
     */
    public function setRoistat($lead_id, $roi_value = null){
        $url="https://".bitrix::$domain.".bitrix24.ru/rest/1/".bitrix::$webhook."/crm.lead.userfield.list";
        $req = (object)array(
            "filter"=>(object)array(
                "LANG"=>"ru"
            )
        );
        $roistat_fields = array();
        $findroistat = json_decode($this->sendRequest($url,$req,"yes"),true)['result'];
        foreach ($findroistat as $a => $value){
            foreach ($value as $b => $item){
//                if($item=="roistat"){
                $test = strripos($value[$b],"RoiStat");
                if($test!==false){
                    array_push($roistat_fields,$value['FIELD_NAME']);
                    break;
                }
            }
        }
        $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.lead.update";
        foreach ($roistat_fields as $a => $value){
            $req = '{"ID":"' . $lead_id . '","fields":{"' . $value . '":"' . $roi_value . '"}}';
            $this->sendRequest($url,$req,null);
        }
    }
}