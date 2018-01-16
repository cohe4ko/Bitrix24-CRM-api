<?php

/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 01.12.2017
 * Time: 13:48
 */
namespace Bitrix;

require_once("bitrix.php");

class deal extends Base
{
    protected $deal_type;
    protected $pipeline_id;

    public function setClassName()
    {
        $this->className = 'deal';
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
        $this->status = $dataRaw['STAGE_ID'];
        $this->deal_type = $dataRaw['TYPE_ID'];
        $this->pipeline_id = $dataRaw['CATEGORY_ID'];
    }
    public function getContact(){
        return $contact = new contact($this->contact_id);
    }
    /**
     * @return mixed
     */
//    public function getList()
//    {
//        $request = file_get_contents("https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.deal.list");
//        return json_decode($request, true);
//    }


    /**
     * @param $title
     * @param $status
     * @param $lead_id
     * @param null $f_name
     * @param null $l_name
     * @param null $phone
     * @param null $contact_im
     * @param null $email
     * @param null $resp_id
     * @param string $type_sum
     * @param null $sum
     * @param null $custom_field
     * @param null $custom_field_val
     * @return array [craeted_contact_id[0],created_deal_id[1]]
     */
    public function addDeal($title, $status, $lead_id,$contact_id,$phone=null, $resp_id = null,$sum = null, $type_sum = "RUB" )
    {
        $contact = new contact();
        $find_contact = $contact->findContact($phone);
        if ($contact_id==null&&$phone!==null&&$find_contact['total'] !== 0) {
            $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.deal.add";
            $lead_data = (object)array(
                "fields" => (object)array(
                    "TITLE" => "$title",
                    "STAGE_ID" => "$status",
                    "OPENED" => "Y",
                    "ASSIGNED_BY_ID" => "$resp_id",
                    "CURRENCY_ID" => "$type_sum",
                    "OPPORTUNITY" => "$sum",
                    "LEAD_ID" => "$lead_id",
                    "CONTACT_ID" => $find_contact['result']['0']['ID']
                )
            );
            $data = $lead_data;
            $result = $this->sendRequest($url, $data, "yes");
            $dealid = json_decode($result, true)['result'];
            return $dealid;
        }
        else{
            $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.deal.add";
            $lead_data = (object)array(
                "fields" => (object)array(
                    "TITLE" => "$title",
                    "STAGE_ID" => "$status",
                    "OPENED" => "Y",
                    "ASSIGNED_BY_ID" => "$resp_id",
                    "CURRENCY_ID" => "$type_sum",
                    "OPPORTUNITY" => "$sum",
                    "LEAD_ID" => "$lead_id",
                    "CONTACT_ID" => $contact_id
                )
            );
            $data = $lead_data;
            $result = $this->sendRequest($url, $data, "yes");
            $dealid = json_decode($result, true)['result'];
            return $dealid;
        }
    }

    /**
     * @param $deal_id
     * @param $title
     * @param $deadline
     * @param $resp_id
     */
    public function addTask($deal_id=null, $title, $deadline, $resp_id)
    {
        $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/task.item.add";
        $task_data = null;
        $task_data = (object)array(
            "arNewTaskData" => (object)array(
                "TITLE" => $title,
                "DEADLINE" => $deadline,
                "RESPONSIBLE_ID" => $resp_id,
                "UF_CRM_TASK" => "D_$deal_id"
            )
        );
        $this->sendRequest($url,$task_data,"yes");
    }

    /**
     * @param $lead_id
     * @param null $roi_value
     */
    public function setRoistat($lead_id, $roi_value = null){
        $url="https://".bitrix::$domain.".bitrix24.ru/rest/1/".bitrix::$webhook."/crm.deal.userfield.list";
        $req = (object)array(
            "filter"=>(object)array(
                "LANG"=>"ru"
            )
        );
        $roistat_fields = array();
        $findroistat = json_decode($this->sendRequest($url,$req,"yes"),true)['result'];
        foreach ($findroistat as $a => $value){
            if(mb_strtolower($value['EDIT_FORM_LABEL'])=='roistat'){
                array_push($roistat_fields,$value['FIELD_NAME']);
            }
//            $roi_key = array_search("roistat",$value);//TODO поиск без учета регистра
//            if(!empty($roi_key)) {
////                echo '<pre>';
////                var_dump($value);
////                echo '</pre>';
//            array_push($roistat_fields,$value['FIELD_NAME']);
//            }
        }
        $url = "https://" . bitrix::$domain . ".bitrix24.ru/rest/1/" . bitrix::$webhook . "/crm.deal.update";
        foreach ($roistat_fields as $a => $value){
            $req = '{"ID":"' . $lead_id . '","fields":{"' . $value . '":"' . $roi_value . '"}}';
            $this->sendRequest($url,$req,null);
        }
    }
}