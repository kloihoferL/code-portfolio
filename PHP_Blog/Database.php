<?php

class Database
{
    private static mysqli $mysqli;

    public static function loadConfig(string $pathToConfigurationFile)
    {
        require_once $pathToConfigurationFile;

    }

    public static function deleteQuery(string $query):bool
    {
        Database::connect();
        $result = Database::$mysqli->query($query);
        Database::disconnect();

        return $result;
    }


    public static function selectQuery(string $query):bool|mysqli_result
    {
        Database::connect();
        $result = Database::$mysqli->query($query);
        Database::disconnect();
        return $result;
    }


    public static function insertQuery(string $query): int
    {
        Database::connect();

        // Fehlerüberprüfung bei der Ausführung der Abfrage
        if (!Database::$mysqli->query($query)) {
            // Fehler protokollieren
            error_log("Fehler bei der SQL-Abfrage: " . Database::$mysqli->error);
            Database::disconnect();
            return -1; // oder eine andere Fehler-ID
        }

        // Überprüfen, ob die Einfügung erfolgreich war und eine ID zurückgegeben wurde
        $id = Database::$mysqli->insert_id;
        if ($id == 0) {
            // Fehler protokollieren, wenn keine ID zurückgegeben wurde
            error_log("Einfügeoperation fehlgeschlagen, keine ID zurückgegeben");
            Database::disconnect();
            return -1; // oder eine andere Fehler-ID
        }

        Database::disconnect();
        return $id;
    }


    public static function updateQuery(string $query):bool
    {
        Database::connect();
        $result = Database::$mysqli->query($query);
        Database::disconnect();

        return $result;
    }

    private static function connect():bool
    {
        if (! Database::$mysqli = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD, DB_DATABASE))
        {
            echo "Could not connect to database.";
            return false;
        }

        return true;
    }

    private static function disconnect():bool
    {
        if (Database::$mysqli != null)
        {
            Database::$mysqli->close();
            return true;
        }

        return false;
    }


}