<?php

class SmsHandler extends EndpointHandler
{

    function __construct()
    {
        parent::__construct();
        $this->endpoint = "sms";
    }

    protected function get()
    {
        if ($this->id != null) {
            $data = DatabaseHelper::queryForSingleRow("SELECT * FROM sms_archive WHERE id = {$this->id}");
            if (!$data) {
                $this->returnError("No SMS with id = {$this->id}!", 400);
            }
            $sms = Sms::fromDatabase($data);

            $this->returnJson($sms);
        } else {

            $processedCondition = $this->hasParameter('processed') ? "processed={$this->getParameter('processed')}" : "1";
            $directionCondition = $this->hasParameter('direction') ? "direction='{$this->getParameter('direction')}'" : "1";
            $phoneCondition = $this->hasParameter('phone') ? "phone='{$this->getParameter('phone')}'" : "1";

            $messages = DatabaseHelper::queryForAllRows("SELECT * FROM sms_archive "
                . "WHERE $processedCondition "
                . "AND $directionCondition "
                . "AND $phoneCondition");
            if (!$messages) {
                $this->returnJson(array());
            }

            $smsList = array();
            foreach ($messages as $message) {
                $smsList[] = Sms::fromDatabase($message);
            }

            $this->returnJson($smsList);
        }
    }

    protected function process()
    {
        if ($this->id == null || !is_numeric($this->id)) {
            $this->returnError("Invalid SMS id ({$this->id}) to process!", 400);
        }

        $data = DatabaseHelper::queryForSingleRow("SELECT * FROM sms_archive WHERE id = {$this->id}");
        if (!$data) {
            $this->returnError("No SMS with id = $this->id!", 400);
        }
        $sms = Sms::fromDatabase($data);

        if ($sms->isProcessed()) {
            $this->returnError("SMS already processed!", 400);
        }

        $game = Game::getActiveGame();
        if (!$game) {
            $this->returnError("No active Game!", 500);
        }

        $processResult = $game->processSms($sms);
        if (array_key_exists("error", $processResult)) {
            $this->returnError("Could not process SMS!\n{$processResult['error']}", 500);
        }

        DatabaseHelper::query("UPDATE sms_archive SET processed = 1 WHERE id = {$this->id}");
        $sms->setProcessed(true);

        $this->returnJson(array("result" => $processResult['result'], "sms" => $sms));
    }

}