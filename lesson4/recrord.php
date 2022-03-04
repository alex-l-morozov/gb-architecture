<?php
interface DBRecord
{
    public function getTableName();
}

class MySQLRecord implements DBRecord
{
    function getTableName()
    {
        // TODO: Implement getTableName() method.
    }
}

class OracleRecord implements DBRecord
{
    function getTableName()
    {
        // TODO: Implement getTableName() method.
    }
}

class PostgreSQLRecord implements DBRecord
{
    function getTableName()
    {
        // TODO: Implement getTableName() method.
    }
}