<?php

class Logger
{

    private static function log($path, $message, $context = "")
    {
        if (!Config::FILE_LOGGING_ENABLED) {
            return;
        }

        try
        {
            $file = fopen($path, "a");
            if ($file) {
                $date = date("Y-m-d H:i:s");
                fwrite($file, "$date\t$context\t$message\n");
                fclose($file);
            }

        } catch (Exception $e) {
            throw new ErrorException("Could not write to log file!\nFile path: " . $path);
        }
    }

    public static function getLog($log)
    {
        $path = Config::LOGS_DIR . "/$log.log";
        try
        {
            return file_get_contents($path);

        } catch (Exception $e) {
            throw new ErrorException("Could not read log file!\nFile path: " . $path);
        }
    }

    public static function logError($message, $context = "generic error")
    {
        self::log(Config::LOGS_DIR . "/err.log", $message, $context);
    }

    public static function logGameEvent($message, $context = "generic game event")
    {
        self::log(Config::LOGS_DIR . "/game.log", $message, $context);
    }

    public static function logSMS($message, $context = "generic SMS")
    {
        self::log(Config::LOGS_DIR . "/sms.log", $message, $context);
    }
}