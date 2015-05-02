<?php

class GameHandler extends EndpointHandler
{

    function __construct()
    {
        parent::__construct();
        $this->endpoint = "game";
    }

    protected function get()
    {
        if ($this->id != null) {

            $data = DatabaseHelper::queryForSingleRow("SELECT * FROM game WHERE id = {$this->id}");
            if (!$data) {
                $this->returnError("No Game with id = {$this->id}!", 400);
            }
            $game = Game::fromDatabase($data);

            $this->returnJson($game);

        } else {

            $activeCondition = $this->hasParameter('active') ? "active={$this->getParameter('active')}" : "1";

            $games = DatabaseHelper::queryForAllRows("SELECT * FROM game WHERE {$activeCondition}");

            if (!$games) {
                $this->returnJson(array());
            }

            $gamesList = array();
            foreach ($games as $game) {
                $gamesList[] = Game::fromDatabase($game);
            }

            $this->returnJson($gamesList);
        }
    }

}