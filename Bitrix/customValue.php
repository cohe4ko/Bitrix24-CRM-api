<?php
/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 15.01.2018
 * Time: 18:32
 */

namespace Bitrix;

class customValue
{
    protected $id;
    protected $value_type;
    protected $value;
    protected $type_id;

    public function __construct($dataRaw)
    {
        $this->id = isset($dataRaw['ID'])?$dataRaw['ID']:null;
        $this->value_type = isset($dataRaw['VALUE_TYPE'])?$dataRaw['VALUE_TYPE']:null;
        $this->value = isset($dataRaw['VALUE'])?$dataRaw['VALUE']:null;
        $this->type_id = isset($dataRaw['TYPE_ID'])?$dataRaw['TYPE_ID']:null;
    }
    public function getRaw(){
        $raw = array(
            "ID"=>$this->id,
            "VALUE_TYPE"=>$this->value_type,
            "VALUE"=>$this->value,
            "TYPE_ID"=>$this->type_id
        );
        return $raw;
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValueType()
    {
        return $this->value_type;
    }

    /**
     * @param mixed $value_type
     */
    public function setValueType($value_type)
    {
        $this->value_type = $value_type;
    }
}