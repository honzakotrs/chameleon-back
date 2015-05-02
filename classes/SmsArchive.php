<?php

class SmsArchive
{

    public static function archiveSMS($phone, $text, $direction, $transmissionDate)
    {
        $text = trim($text);

        $query = "INSERT INTO sms_archive(transmision_date,phone,msg,direction) "
            . "VALUES('$transmissionDate','$phone','$text','$direction')";
        $smsId = DatabaseHelper::insert($query);

        Logger::logSMS("$text", "$phone | $direction");

        if ($smsId) {
            return $smsId;
        } else {
            Logger::logError("Could not save SMS to the database! (".DatabaseHelper::getLastError().")", "SMS Archive");
            return false;
        }
    }

}