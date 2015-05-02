<?php

class TicketHandler extends EndpointHandler
{

    function __construct()
    {
        parent::__construct();
        $this->endpoint = "ticket";
    }

    protected function get()
    {
        if ($this->id != null) {
            $data = DatabaseHelper::queryForSingleRow("SELECT * FROM ticket WHERE id = {$this->id}");
            if (!$data) {
                $this->returnError("No Ticket with id = {$this->id}!", 400);
            }
            $ticket = Ticket::fromDatabase($data);

            $this->returnJson($ticket);
        } else {

            $typeCondition = $this->hasParameter('type') ? "type = '{$this->getParameter('type')}'" : "1";
            $codeCondition = $this->hasParameter('code') ? "ticket_code LIKE '{$this->getParameter('code')}'" : "1";

            $tickets = DatabaseHelper::queryForAllRows("SELECT * FROM ticket "
                . "WHERE $typeCondition "
                . "AND $codeCondition ");
            if (!$tickets) {
                $this->returnJson(array());
            }

            $ticketsList = array();
            foreach ($tickets as $t) {
                $ticketsList[] = Ticket::fromDatabase($t);
            }

            $this->returnJson($ticketsList);
        }
    }

}