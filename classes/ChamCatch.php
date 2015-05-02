<?php

class ChamCatch implements \JsonSerializable {

    private $id;
    private $catchDate;
    private $stopId;
    private $teamId;
    private $chameleonId;

    function __construct($catchDate, $stopId, $teamId, $chameleonId)
    {
        $this->catchDate = $catchDate;
        $this->stopId = $stopId;
        $this->teamId = $teamId;
        $this->chameleonId = $chameleonId;
    }

    public static function fromDatabase($data) {
        $catch = new ChamCatch($data['catch_date'], $data['stop_id'], $data['team_id'], $data['chameleon_id']);
        $catch->id = $data['id'];
        return $catch;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCatchDate()
    {
        return $this->catchDate;
    }

    /**
     * @return mixed
     */
    public function getStopId()
    {
        return $this->stopId;
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
    public function getChameleonId()
    {
        return $this->chameleonId;
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
            "catch_date" => $this->catchDate,
            "stop_id" => (int)$this->stopId,
            "team_id" => (int)$this->teamId,
            "chameleon_id" => (int)$this->chameleonId);
    }
}