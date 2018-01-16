<?php
/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 11.01.2018
 * Time: 21:04
 */

namespace Bitrix;


abstract class Base
{
    protected $className;
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var int
     */
    protected $responsible_id;
    protected $date_create;
    protected $date_update;
    protected $status;
    protected $currency;
    protected $summ;
    protected $comment;
    protected $lead_source;
    protected $source_description;
    /**
     * @var customValue[]
     */
    protected $phone;
    protected $email;
    protected $site;
    protected $messenger;
    protected $contact_id;
    protected $name;
    protected $family_name;
    protected $otchestvo;
    protected $created_by_id;
    protected $modified_by_id;

    /**
     * @return mixed
     */
    public function getModifiedById()
    {
        return $this->modified_by_id;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getContactId()
    {
        return $this->contact_id;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDateCreate()
    {
        return $this->date_create;
    }

    /**
     * @return mixed
     */
    public function getDateUpdate()
    {
        return $this->date_update;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return $this->family_name;
    }

    /**
     * @param mixed $family_name
     */
    public function setFamilyName($family_name)
    {
        $this->family_name = $family_name;
    }

    /**
     * @return mixed
     */
    public function getOtchestvo()
    {
        return $this->otchestvo;
    }

    /**
     * @param mixed $otchestvo
     */
    public function setOtchestvo($otchestvo)
    {
        $this->otchestvo = $otchestvo;
    }

    /**
     * @param mixed $responsible_id
     */
    public function setResponsibleId($responsible_id)
    {
        $this->responsible_id = $responsible_id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $phoneRaw = array(
                    "VALUE_TYPE"=>"MOBILE",
//                    "TYPE_ID"=>"PHONE",
                    "VALUE"=>"$phone"
        );
        $ph = new customValue($phoneRaw);
        $this->phone[] = $ph;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    protected abstract function setClassName();
    /**
     * @param $id
     * @return mixed
     */
    protected function loadById($id){
        $result = bitrix::sendRequest("crm.$this->className.get?id=$id");
        echo '<pre>';
        var_dump($result);
        echo '</pre>';
        die;
        $this->loadInRaw($result['result']);
    }
    public function getResponsibleId(){
        return $this->responsible_id;
    }
    public function loadInRaw($dataRaw){
        $this->id = $dataRaw['ID'];
        $this->title = isset($dataRaw['TITLE'])?$dataRaw['TITLE']:null;
        $this->responsible_id = isset($dataRaw['ASSIGNED_BY_ID'])?$dataRaw['ASSIGNED_BY_ID']:null;
        $this->date_create = isset($dataRaw['DATE_CREATE'])?$dataRaw['DATE_CREATE']:null;
        $this->date_update = isset($dataRaw['DATE_MODIFY'])?$dataRaw['DATE_MODIFY']:null;
        $this->summ = isset($dataRaw['OPPORTUNITY'])?$dataRaw['OPPORTUNITY']:null;
        $this->currency = isset($dataRaw['CURRENCY_ID'])?$dataRaw['CURRENCY_ID']:null;
        $this->comment = isset($dataRaw['COMMENTS'])?$dataRaw['COMMENTS']:null;
        $this->lead_source = isset($dataRaw['SOURCE_ID'])?$dataRaw['SOURCE_ID']:null;
        $this->source_description = isset($dataRaw['SOURCE_DESCRIPTION'])?$dataRaw['SOURCE_DESCRIPTION']:null;
        $this->site = isset($dataRaw['WEB'])?$dataRaw['WEB']:null;
        if(isset($dataRaw['PHONE'])) {
            foreach ($dataRaw['PHONE'] as $item) {
                $this->phone[] = new customValue($item);
            }
        }
        else
            $dataRaw['PHONE']=null;
        if (isset($dataRaw['EMAIL'])) {
            foreach ($dataRaw['EMAIL'] as $item) {
                $this->email[] = new customValue($item);
            }
        }
        else
            $dataRaw['EMAIL']=null;
        if(isset($dataRaw['WEB'])) {
            foreach ($dataRaw['WEB'] as $item) {
                $this->site[] = new customValue($item);
            }
        }
        else
            $dataRaw['WEB']=null;
        if (isset($dataRaw['IM'])){
            foreach ($dataRaw['IM'] as $item) {
                $this->messenger[] = new customValue($item);
            }
        }
        else
            $dataRaw["IM"]=null;
        $this->contact_id = isset($dataRaw['CONTACT_ID'])?$dataRaw['CONTACT_ID']:null;
        $this->name = isset($dataRaw['NAME'])?$dataRaw['NAME']:null;
        $this->family_name = isset($dataRaw['LAST_NAME'])?$dataRaw['LAST_NAME']:null;
        $this->otchestvo = isset($dataRaw['SECOND_NAME'])?$dataRaw['SECOND_NAME']:null;
        $this->created_by_id = isset($dataRaw['CREATED_BY_ID'])?$dataRaw['CREATED_BY_ID']:null;
        $this->modified_by_id = isset($dataRaw['MODIFY_BY_ID'])?$dataRaw['MODIFY_BY_ID']:null;
    }
    public function save(){
        if (!empty($this->id)){
            $url = "crm.$this->className.update";
            $request = array(
                "ID"=>$this->id,
                "fields"=>array(
                    "TITLE"=>$this->title,
                    "ASSIGNED_BY_ID"=>$this->responsible_id,
                )
            );
            $answer = bitrix::sendRequest($url,$request);
//            echo '<pre>';
            var_dump($answer);
        }
        else{
            $url = "crm.$this->className.add";
            $phones = array();
            foreach ($this->phone as $fields){
                $phones[]=$fields->getRaw();
            }
            $request = array(
                "fields"=>array(
                    "TITLE"=>$this->title,
                    "ASSIGNED_BY_ID"=>$this->responsible_id,
                    "NAME"=>$this->name,
                    "SECOND_NAME"=>$this->otchestvo,
                    "LAST_NAME"=>$this->family_name,
                    "PHONE"=>$phones
                ),
                'params' => array(
                "REGISTER_SONET_EVENT" => "Y"
                    )
            );
            $answer = bitrix::sendRequest($url,$request);
            echo '<pre>';
            print_r($answer);
        }
    }
}