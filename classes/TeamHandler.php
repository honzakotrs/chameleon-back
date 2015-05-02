<?php

class TeamHandler extends EndpointHandler
{

    function __construct()
    {
        parent::__construct();
        $this->endpoint = "team";
    }

    protected function get()
    {
        if ($this->id != null) {
            $data = DatabaseHelper::queryForSingleRow("SELECT * FROM team WHERE id = {$this->id}");
            if (!$data) {
                $this->returnError("No Team with id = {$this->id}!", 400);
            }
            $team = Team::fromDatabase($data);

            $this->returnJson($team);
        } else {

            $phoneCondition = $this->hasParameter('phone') ? "phone = '{$this->getParameter('phone')}'" : "1";
            $activeCondition = $this->hasParameter('active') ? "active = {$this->getParameter('active')}" : "1";
            $roleCondition = $this->hasParameter('role') ? "role = {$this->getParameter('role')}" : "1";

            $teams = DatabaseHelper::queryForAllRows("SELECT * FROM team "
                . "WHERE {$phoneCondition} "
                . "AND {$activeCondition} "
                . "AND {$roleCondition} ");
            if (!$teams) {
                $this->returnJson(array());
            }

            $teamsList = array();
            foreach ($teams as $t) {
                $teamsList[] = Team::fromDatabase($t);
            }

            $this->returnJson($teamsList);
        }
    }

}