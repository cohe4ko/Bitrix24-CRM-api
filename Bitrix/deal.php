<?php

/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 01.12.2017
 * Time: 13:48
 */
namespace Bitrix;

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