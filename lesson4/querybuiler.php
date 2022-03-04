<?php
interface DBQueryBuilder
{
    public function SelectQuery();
    public function InsertQuery();
    public function UpdateQuery();
    public function DeleteQuery();
}

class MySQLQueryBuilder implements DBQueryBuilder
{
    public function SelectQuery()
    {
        // TODO: Implement SelectQuery() method.
    }
    public function InsertQuery()
    {
        // TODO: Implement InsertQuery() method.
    }
    public function UpdateQuery()
    {
        // TODO: Implement UpdateQuery() method.
    }
    public function DeleteQuery()
    {
        // TODO: Implement DeleteQuery() method.
    }
}

class OracleQueryBuilder implements DBQueryBuilder
{
    public function SelectQuery()
    {
        // TODO: Implement SelectQuery() method.
    }
    public function InsertQuery()
    {
        // TODO: Implement InsertQuery() method.
    }
    public function UpdateQuery()
    {
        // TODO: Implement UpdateQuery() method.
    }
    public function DeleteQuery()
    {
        // TODO: Implement DeleteQuery() method.
    }
}

class PostgreSQLQueryBuilder implements DBQueryBuilder
{
    public function SelectQuery()
    {
        // TODO: Implement SelectQuery() method.
    }
    public function InsertQuery()
    {
        // TODO: Implement InsertQuery() method.
    }
    public function UpdateQuery()
    {
        // TODO: Implement UpdateQuery() method.
    }
    public function DeleteQuery()
    {
        // TODO: Implement DeleteQuery() method.
    }
}