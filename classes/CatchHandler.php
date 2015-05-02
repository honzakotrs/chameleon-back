<?php

class CatchHandler extends EndpointHandler {

    function __construct()
    {
        parent::__construct();
        $this->endpoint = "catch";
    }

    protected function get()
    {
        if ($this->id != null) {
            $data = DatabaseHelper::queryForSingleRow("SELECT * FROM catch WHERE id = {$this->id}");
            if (!$data) {
                $this->returnError("No Catch with id = {$this->id}!", 400);
            }
            $catch = Move::fromDatabase($data);

            $this->returnJson($catch);
        } else {

            $teamCondition = $this->hasParameter('team_id') ? "team_id={$this->getParameter('team_id')}" : "1";
            $chameleonCondition = $this->hasParameter('chameleon_id') ? "chameleon_id={$this->getParameter('chameleon_id')}" : "1";

            $catches = DatabaseHelper::queryForAllRows("SELECT * FROM catch "
                . "WHERE $teamCondition "
                . "AND $chameleonCondition ");
            if (!$catches) {
                $this->returnJson(array());
            }

            $catchesList = array();
            foreach ($catches as $m) {
                $catchesList[] = Move::fromDatabase($m);
            }

            $this->returnJson($catchesList);
        }
    }

}