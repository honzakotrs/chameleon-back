<?php

require_once "class_list.php";

$sender = HttpUtils::ping(Config::PORTAL_SENDER_HOST.":".Config::PORTAL_SENDER_HOST_PORT);
$database = HttpUtils::ping(Config::DB_HOST);

$status = array();
$status['sender_host'] = $sender ? $sender['total_time'].' ms' : 'Unreachable';
$status['database_host'] = $database ? $database['total_time'].' ms' : 'Unreachable';
$status['database_auth_ok'] = DatabaseHelper::hasWorkingConnection();

HttpUtils::returnJson($status);
