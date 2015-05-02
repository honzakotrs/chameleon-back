<?php

class DatabaseHelper
{

    private static $lastError;

    private static function createMySqli()
    {
        $mysqli = new mysqli("p:" . Config::DB_HOST, Config::DB_USER, Config::DB_PWD, Config::DB_NAME);

        if ($mysqli->connect_errno)
            throw new Exception("Cannot establish connection to the database!\n" . $mysqli->connect_errno);

        $mysqli->set_charset("utf8");

        return $mysqli;
    }

    public static function getLastError() {
        return self::$lastError;
    }

    public static function hasWorkingConnection()
    {
        try {
            self::createMySqli();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function queryForSingleRow($query)
    {
        $mysqli = self::createMySqli();
        $result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $row;
    }

    public static function queryForSingleField($query, $fieldName)
    {
        $mysqli = self::createMySqli();
        $result = $mysqli->query($query);

        if (!$result) {
            self::$lastError = $mysqli->error;
            return false;
        }

        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $row[$fieldName];
    }

    public static function queryForAllRows($query, $keyColumn = null)
    {
        $mysqli = self::createMySqli();
        $result = $mysqli->query($query);

        if (!$result) {
            self::$lastError = $mysqli->error;
            return false;
        }

        $rows = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if ($keyColumn != null) {
                $rows[$row[$keyColumn]] = $row;
            } else {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    public static function query($query)
    {
        $mysqli = self::createMySqli();
        $result = $mysqli->query($query);

        if (!$result) {
            self::$lastError = $mysqli->error;
            return false;
        }

        return $result;
    }

    public static function insert($query)
    {
        $mysqli = self::createMySqli();
        $result = $mysqli->query($query);
        if (!$result) {
            self::$lastError = $mysqli->error;
            return false;
        }

        return $mysqli->insert_id;
    }
}