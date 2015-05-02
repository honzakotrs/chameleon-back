<?php

abstract class EndpointHandler
{

    protected $jsonParams;
    protected $endpoint;
    protected $action;
    protected $id;

    public function __construct()
    {

        $this->action = isset($_GET['action']) ? trim($_GET['action']) : "get";
        if ($this->action == "") {
            $this->returnError("No action specified!", 400);
        }

        if (isset($_GET['id'])) {
            $this->id = $_GET['id'];
            if (!is_numeric($this->id)) {
                $this->returnError("Non-numeric id!", 400);
            }
        }

        // .. check if we got JSON params
        $jsonData = file_get_contents('php://input');
        $this->jsonParams = $jsonData ? json_decode($jsonData) : null;
    }

    public function handleAction()
    {
        if (!method_exists($this, $this->action)) {
            $this->returnError("Unknown action ({$this->action}) to handle!", 400);
        }
        return $this->{$this->action}();
    }

    protected function hasParameter($name)
    {
        if ($this->jsonParams == null) {
            return false;
        }
        return isset($this->jsonParams->{$name});
    }

    protected function getParameter($name)
    {
        return $this->jsonParams->{$name};
    }

    protected function returnError($message, $code)
    {
        $label = "endpoint/{$this->endpoint}/{$this->action}";
        if ($this->id) $label .= "/{$this->id}";
        Logger::logError($message, $label);
        HttpUtils::returnJson(array("error" => $message), $code);
    }

    protected function returnJson($object, $code = 200)
    {
        HttpUtils::returnJson($object, $code);
    }
}