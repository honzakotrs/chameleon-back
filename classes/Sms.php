<?php

class Sms implements \JsonSerializable {

    private $id = null;
    private $phone;
    private $text;
    private $direction;
    private $smsc;
    private $date;
    private $processed;

    function __construct($params = array()) {
        $this->direction = isset($params[Config::PORTAL_PARAM_DIRECTION]) ? $params[Config::PORTAL_PARAM_DIRECTION] : "in";
        $this->phone = isset($params[Config::PORTAL_RECEIVER_PARAM_PHONE]) ? $params[Config::PORTAL_RECEIVER_PARAM_PHONE] : null;
        $this->text = isset($params[Config::PORTAL_RECEIVER_PARAM_TEXT]) ? $params[Config::PORTAL_RECEIVER_PARAM_TEXT] : null;
        $this->smsc = isset($params[Config::PORTAL_RECEIVER_PARAM_SMSC]) ? $params[Config::PORTAL_RECEIVER_PARAM_SMSC] : null;
    }

    public static function fromDatabase($data) {
        $sms = new Sms();
        $sms->date = $data['transmision_date'];
        $sms->direction = $data['direction'];
        $sms->id = $data['id'];
        $sms->phone = $data['phone'];
        $sms->processed = $data['processed'];
        $sms->text = $data['msg'];
        return $sms;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return mixed
     */
    public function getSmsc()
    {
        return $this->smsc;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setProcessed($processed)
    {
        $this->processed = $processed;
    }

    public function isProcessed()
    {
        return $this->processed;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return array("id" => (int)$this->id,
            "direction" => $this->direction,
            "processed" => (int)$this->processed,
            "phone" => $this->phone,
            "text" => $this->text,
            "date" => $this->date,
            "smsc" => $this->smsc);
    }
}