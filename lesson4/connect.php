<?php
interface DBConnection
{
    public function getConnection();
}

class MySQLConnection implements DBConnection
{
    public function getConnection()
    {
        // TODO: Implement MySQLConnection() method.
    }
}

class OracleConnection implements DBConnection
{
    public function getConnection()
    {
        // TODO: Implement MySQLConnection() method.
    }
}

class PostgreSQLConnection implements DBConnection
{
    public function getConnection()
    {
        // TODO: Implement MySQLConnection() method.
    }
}