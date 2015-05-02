<?php

class Move implements \JsonSerializable
{

    private $id;
    private $stopFrom;
    private $stopVia;
    private $stopTo;
    private $moveDate;
    private $transportType;
    private $ticketCode;
    private $teamId;
    private $type;

    public function __construct($stopFrom, $stopVia, $stopTo, $transportType, $ticketCode)
    {
        $this->stopFrom = $stopFrom;
        $this->stopVia = $stopVia;
        $this->stopTo = $stopTo;
        $this->transportType = $transportType;
        $this->ticketCode = $ticketCode;
    }

    public static function fromDatabase($data)
    {
        $move = new Move($data['stop_id_from'],
            $data['stop_id_inter'],
            $data['stop_id_to'],
            $data['transport_type'],
            $data['ticket_code']);
        $move->id = $data['id'];
        $move->teamId = $data['team_id'];
        $move->type = $data['type'];
        $move->moveDate = $data['move_date'];

        return $move;
    }

    /**
     * @param mixed $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $moveDate
     */
    public function setMoveDate($moveDate)
    {
        $this->moveDate = $moveDate;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getStopFrom()
    {
        return $this->stopFrom;
    }

    /**
     * @return mixed
     */
    public function getStopVia()
    {
        return $this->stopVia;
    }

    /**
     * @return mixed
     */
    public function getStopTo()
    {
        return $this->stopTo;
    }

    /**
     * @return mixed
     */
    public function getTransportType()
    {
        return $this->transportType;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
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
    public function getMoveDate()
    {
        return $this->moveDate;
    }

    /**
     * @return mixed
     */
    public function getTicketCode()
    {
        return $this->ticketCode;
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
            "stop_from" => $this->stopFrom != null ? (int)$this->stopFrom : null,
            "stop_via" => $this->stopVia != null ? (int)$this->stopVia : null,
            "stop_to" => (int)$this->stopTo,
            "transport_type" => $this->transportType,
            "type" => $this->type,
            "team_id" => (int)$this->teamId,
            "ticket_code" => $this->ticketCode,
            "move_date" => $this->moveDate);
    }
}