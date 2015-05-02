<?php

class Game implements \JsonSerializable
{

    private $id;
    private $title;
    private $startDate;
    private $active;
    private static $TRAIN_STOP_IDS = array(8, 16, 41, 46);
    const MOVE_SMS_REGEX = '/^([0-9]{1,2}) ([0-9]{0,2} )?([0-9]{1,2}) ([SBVPT]) (\w{4})$/';
    const STOP_SMS_REGEX = '/^([0-9]{1,2}) STOP$/';
    const CATCH_SMS_REGEX = '/^CHCH ([0-9]{1,2}) ([0-9]{1,2})$/';

    private function __construct()
    {

    }

    public static function getActiveGame()
    {
        $data = DatabaseHelper::queryForSingleRow("SELECT * FROM game WHERE active = 1");
        if (!$data) {
            return false;
        }
        return Game::fromDatabase($data);
    }

    public static function fromDatabase(array $data)
    {
        $game = new Game();
        $game->id = $data['id'];
        $game->title = $data['title'];
        $game->startDate = $data['start_date'];
        $game->active = $data['active'];
        return $game;
    }

    public function processSms(Sms $sms)
    {
        $team = Team::getTeamByPhone($sms->getPhone());
        if (!$team) {
            Logger::logGameEvent("Could not locate team by phone {$sms->getPhone()}");
            return array("error" => "Could not locate team by phone {$sms->getPhone()}");
        }

        $text = strtoupper($sms->getText());
        $parts = array();
        if (preg_match(self::MOVE_SMS_REGEX, $text, $parts)) {

            $from = $parts[1];
            $via = $parts[2] != "" ? str_replace(" ", "", $parts[2]) : null;
            $to = $parts[3];
            $transportType = strtoupper($parts[4]);
            $ticketCode = strtoupper($parts[5]);
            $trainStops = in_array($from, self::$TRAIN_STOP_IDS) && in_array($to, self::$TRAIN_STOP_IDS);

            if (!$this->isValidStopId($from)
                || !$this->isValidStopId($to)
                || ($via != "" && !$this->isValidStopId($via))
            ) {
                Logger::logGameEvent("Invalid stop number(s): $from - ($via) - $to");
                return array("error" => "Invalid stop number(s): $from - ($via) - $to");
            }

            if ($team->getRole() == Config::TEAM_ROLE_PLAYER) {
                $ticket = Ticket::getTicket($ticketCode);
                if (!$ticket) {
                    Logger::logGameEvent("Ticket with code {$ticketCode} was not issued!");
                    return array("error" => "Ticket with code {$ticketCode} was not issued!");
                }
                if (Ticket::isTicketUsed($ticketCode)) {
                    Logger::logGameEvent("Ticket with code {$ticketCode} has been already used!");
                    return array("error" => "Ticket with code {$ticketCode} has been already used!");
                }
                if ($via != null && $ticket->getType() != "D") {
                    Logger::logGameEvent("Bad Ticket type. Ticket with code {$ticketCode} is not DOUBLE!");
                    return array("error" => "Bad Ticket type. Ticket with code {$ticketCode} is not DOUBLE!");
                }
                if ($trainStops && $ticket->getType() != "V") {
                    Logger::logGameEvent("Bad Ticket type. Ticket with code {$ticketCode} is not TRAIN!");
                    return array("error" => "Bad Ticket type. Ticket with code {$ticketCode} is not TRAIN!");
                }
            }

            $move = new Move($from, $via, $to, $transportType, $ticketCode);
            $move->setType($via != null ? "DOUBLE" : "SINGLE");
            $move->setMoveDate($sms->getDate());
            $move->setTeamId($team->getId());

            $moveId = $this->addMove($move);
            if (!$moveId) {
                Logger::logError("Could not add move to the database!\n" . DatabaseHelper::getLastError());
                return array("error" => "Could not add move to the database!");
            }
            $move->setId($moveId);

            return array("result" => $move);

        } elseif (preg_match(self::STOP_SMS_REGEX, $text, $parts)) {

            $to = $parts[1];

            if (!$this->isValidStopId($to)) {
                Logger::logGameEvent("Invalid stop number: $to");
                return array("error" => "Invalid stop number: $to");
            }

            $move = new Move(null, null, $to, null, null);
            $move->setType("STOP");
            $move->setMoveDate($sms->getDate());
            $move->setTeamId($team->getId());

            $moveId = $this->addMove($move);
            if (!$moveId) {
                Logger::logError("Could not add move to the database!\n" . DatabaseHelper::getLastError());
                return array("error" => "Could not add move to the database!");
            }
            $move->setId($moveId);

            return array("result" => $move);

        } elseif (preg_match(self::CATCH_SMS_REGEX, $text, $parts)) {

            $chameleon = $team;
            $team = null;
            $stopId = $parts[1];
            $teamId = $parts[2];

            if (!$this->isValidStopId($stopId)) {
                Logger::logGameEvent("Invalid stop number: $stopId");
                return array("error" => "Invalid stop number: $stopId");
            }
            $team = Team::getTeam($teamId);
            if (!$team) {
                Logger::logGameEvent("Invalid team with id: $teamId");
                return array("error" => "Invalid team with id: $teamId");
            }
            if ($team->getRole() != Config::TEAM_ROLE_PLAYER) {
                Logger::logGameEvent("Team with id does not represent players: $teamId");
                return array("error" => "Team with id does not represent players: $teamId");
            }
            if ($chameleon->getRole() != Config::TEAM_ROLE_CHAMELEON) {
                Logger::logGameEvent("Bad Sender. Team with id does not represent chameleon: {$chameleon->getId()}");
                return array("error" => "Bad Sender. Team with id does not represent chameleon: {$chameleon->getId()}");
            }

            $catch = new ChamCatch($sms->getDate(), $stopId, $teamId, $chameleon->getId());
            $catchId = $this->addCatch($catch);

            if (!$catchId) {
                Logger::logError("Could not add Catch to the database!\n" . DatabaseHelper::getLastError());
                return array("error" => "Could not add Catch to the database!");
            }
            $catch->setId($catchId);

            return array("result" => $catch);
        }

        return array("error" => "SMS in unknown format: {$text}");
    }

    private function isValidStopId($stopId)
    {
        return is_numeric($stopId) && $stopId >= 1 && $stopId <= Config::MAX_STOP_ID;
    }

    private function addMove(Move $move)
    {
        $via = $move->getStopVia() != null ? $move->getStopVia() : "NULL";
        $from = $move->getStopFrom() != null ? $move->getStopFrom() : "NULL";
        $ticketCode = $move->getTicketCode() != null ? "'" . $move->getTicketCode() . "'" : "NULL";
        $transportType = $move->getTransportType() != null ? "'" . $move->getTransportType() . "'" : "NULL";
        return DatabaseHelper::insert(
            "INSERT INTO move (team_id, move_date, stop_id_from, stop_id_to, stop_id_inter, transport_type, ticket_code, type) "
            . " VALUES ({$move->getTeamId()}, '{$move->getMoveDate()}', "
            . "{$from}, {$move->getStopTo()}, {$via}, "
            . "{$transportType}, {$ticketCode}, '{$move->getType()}')");
    }

    private function addCatch(ChamCatch $catch)
    {
        return DatabaseHelper::insert(
            "INSERT INTO catch (catch_date, stop_id, team_id, chameleon_id) "
            . " VALUES ('{$catch->getCatchDate()}', "
            . "{$catch->getStopId()}, {$catch->getTeamId()}, {$catch->getChameleonId()})");
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return array("id" => (int)$this->id,
            "title" => $this->title,
            "active" => (bool)$this->active,
            "start_date" => $this->startDate);
    }
}