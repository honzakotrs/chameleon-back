<?php

class Team implements \JsonSerializable {

    private $id;
    private $name;
    private $phone;
    private $role;
    private $active;

    function __construct($name, $phone, $role)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->role = $role;
    }

    public static function fromDatabase($data) {
        $team = new Team($data['name'], $data['phone'], $data['role']);
        $team->id = $data['id'];
        $team->active = $data['active'];
        return $team;
    }

    public static function getTeamByPhone($phone) {
        $data = DatabaseHelper::queryForSingleRow("SELECT * FROM team WHERE phone = '{$phone}'");
        if (!$data) {
            return false;
        }
        return Team::fromDatabase($data);
    }

    public static function getTeam($id) {
        $data = DatabaseHelper::queryForSingleRow("SELECT * FROM team WHERE id = {$id}");
        if (!$data) {
            return false;
        }
        return Team::fromDatabase($data);
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
    public function getName()
    {
        return $this->name;
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
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
            "name" => $this->name,
            "phone" => $this->phone,
            "role" => (int)$this->role,
            "active" => (int)$this->active);
    }
}