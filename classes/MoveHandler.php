<?php

class MoveHandler extends EndpointHandler
{

    function __construct()
    {
        parent::__construct();
        $this->endpoint = "move";
    }

    protected function get()
    {
        if ($this->id != null) {
            $data = DatabaseHelper::queryForSingleRow("SELECT * FROM move WHERE id = {$this->id}");
            if (!$data) {
                $this->returnError("No Move with id = {$this->id}!", 400);
            }
            $move = Move::fromDatabase($data);

            $this->returnJson($move);
        } else {

            $teamCondition = $this->hasParameter('team_id') ? "team_id={$this->getParameter('team_id')}" : "1";
            $ticketCodeCondition = $this->hasParameter('ticket_code') ? "ticket_code LIKE '{$this->getParameter('ticket_code')}'" : "1";

            $moves = DatabaseHelper::queryForAllRows("SELECT * FROM move "
                . "WHERE $teamCondition "
                . "AND $ticketCodeCondition "
                . " ORDER BY move_date ASC ");
            if (!$moves) {
                $this->returnJson(array());
            }

            $movesList = array();
            foreach ($moves as $m) {
                $movesList[] = Move::fromDatabase($m);
            }

            $this->returnJson($movesList);
        }
    }

}