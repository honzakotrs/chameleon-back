<?php

class Ticket implements \JsonSerializable {

    private $id;
    private $code;
    private $type;

    function __construct($code, $type)
    {
        $this->code = $code;
        $this->type = $type;
    }

    public static function fromDatabase($data) {
        $ticket = new Ticket($data['code'], $data['type']);
        $ticket->id = $data['id'];
        return $ticket;
    }

    public static function getTicket($code) {
        $code = strtoupper($code);
        $data = DatabaseHelper::queryForSingleRow("SELECT * FROM ticket WHERE code = '{$code}'");

        if (!$data) {
            return false;
        }

        return Ticket::fromDatabase($data);
    }

    public static function isTicketUsed($code) {
        $move = DatabaseHelper::queryForSingleRow("SELECT * FROM move WHERE ticket_code = '{$code}'");
        return $move != null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
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
            "code" => $this->code,
            "type" => $this->type);
    }
}