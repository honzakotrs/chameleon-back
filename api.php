<?php

require_once "class_list.php";

if (!isset($_GET['endpoint'])) {
    $message = "No endpoint specified!";
    Logger::logError($message, "api");
    HttpUtils::returnJson(array("error" => $message), 400);
}

$endpoint = strtolower($_GET['endpoint']);
$handler = createHandler($endpoint);

if ($handler == null) {
    $message = "No handler for endpoint {$endpoint}!";
    Logger::logError($message, "api");
    HttpUtils::returnJson(array("error" => $message), 501);
}

$handled = $handler->handleAction();

if (!$handled) {
    $message = "Failed to handle action for endpoint {$endpoint}!";
    Logger::logError($message, "api");
    HttpUtils::returnJson(array("error" => $message), 500);
}

function createHandler($endpoint) {
    switch ($endpoint) {
        case "sms": return new SmsHandler();
        case "game": return new GameHandler();
        case "move": return new MoveHandler();
        case "ticket": return new TicketHandler();
        case "team": return new TeamHandler();
        case "catch": return new CatchHandler();
        default:
            return null;
    }
}