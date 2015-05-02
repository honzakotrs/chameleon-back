<?php

require_once "class_list.php";

const PHONE_REGEX = '/^((\+|00)[0-9]{3})?([0-9]{9}$)/';

$sms = new Sms($_GET);

if ($sms->getPhone() == "" || $sms->getText() == "") {
    handleError("No phone or text, ignoring!", $sms, 400);
}
// .. check and normalize phone number
$phoneParts = array();
if (preg_match(PHONE_REGEX, $sms->getPhone(), $phoneParts)) {
    $sms->setPhone($phoneParts[3]);
} else {
    handleError("Invalid phone number format ({$sms->getPhone()}), ignoring!", $sms, 400);
}

// .. Archive SMS
$sms->setDate(date("Y-m-d H:i:s"));
$smsId = SmsArchive::archiveSMS($sms->getPhone(), $sms->getText(), $sms->getDirection(), $sms->getDate());
if (!$smsId) {
    handleError("Could not archive SMS!", $sms, 500);
}
$sms->setId($smsId);

if ($sms->getDirection() == "out") {

    $err = HttpUtils::sendSms($sms->getPhone(), $sms->getText());

    if ($err) {
        handleError("Could not send outgoing SMS: $err", $sms, 500);
    } else {
        HttpUtils::returnJson(array("sms" => $sms));
    }

} elseif ($sms->getDirection() == "in") {
    HttpUtils::returnJson(array("sms" => $sms));
} else {
    handleError("Unknown SMS direction in/out: {$sms->getDirection()}", $sms, 500);
}

function handleError($message, $sms, $code) {
    Logger::logError($message, "Portal");
    HttpUtils::returnJson(array("error" => $message, "sms" => $sms), $code);
}