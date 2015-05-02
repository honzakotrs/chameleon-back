<?php

class Config
{
    const
        // Database settings
        DB_HOST = 'localhost',
        DB_USER = 'chameleon',
        DB_PWD = 'replace-me-with-password',
        DB_NAME = 'chameleon',

        // Platform settings
        FILE_LOGGING_ENABLED = true,
        LOGS_DIR = '/var/www/chameleon/back/logs',

        // Portal settings
        PORTAL_SENDER_HOST = "192.168.0.100",
        PORTAL_SENDER_HOST_PORT = "8080",
        PORTAL_SENDER_HOST_PWD = "replace-me-with-password",
        PORTAL_SENDER_URL = "http://%s:%s/sendsms?password=%s&phone=%s&text=%s",
        PORTAL_RECEIVER_PARAM_PHONE = "phone",
        PORTAL_RECEIVER_PARAM_TEXT = "text",
        PORTAL_RECEIVER_PARAM_SMSC = "smscenter",
        PORTAL_PARAM_DIRECTION = "direction",

        // Game settings
        MAX_STOP_ID = 51,
        TASK_SCORE = 2,
        CATCH_SCORE = 10,
        BLACK_TICKET_PENALTY = -2,
        TEAM_ROLE_PLAYER = 0,
        TEAM_ROLE_CHAMELEON = 1,
        TEAM_ROLE_OTHER = 99
    ;
}