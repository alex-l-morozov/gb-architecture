<?php
abstract class DBFactory
{
    private object $connect;
    private object $record;
    private object $queryBuilder;

    public function __construct()
    {
        $this->connect = $this->makeConnect();
        $this->record = $this->makeRecord();
        $this->queryBuilder = $this->makeQueryBuilder();
    }

    public function connect(){
        return $this->connect->getConnection();
    }
    public function record($atr){
        return $this->record->makeRecordToDB($atr);
    }

    public function query(){

    }

    abstract protected function makeConnect();
    abstract protected function makeRecord();
    abstract protected function makeQueryBuilder();
}

class MySQLDBFactory extends DBFactory
{

    protected function makeConnect(): DBConnection
    {
        return new MySQLConnect;
    }

    protected function makeRecord() : DBRecord
    {
        return new MySQLRecord;
    }

    protected function makeQueryBuilder() : DBQueryBuilder
    {
        return new MySQLQueryBuilder;
    }
}

class OracleDBFactory extends DBFactory
{

    protected function makeConnect() : DBConnection
    {
        return new OracleConnect;
    }

    protected function makeRecord() : DBRecord
    {
        return new OracleRecord;
    }

    protected function makeQueryBuilder() : DBQueryBuilder
    {
        return new OracleQueryBuilder;
    }
}

class PostgreSQLDBFactory extends DBFactory
{

    protected function makeConnect() : DBConnection
    {
        return new PostgreSQLConnect;
    }

    protected function makeRecord() : DBRecord
    {
        return new PostgreSQLRecord;
    }

    protected function makeQueryBuilder() : DBQueryBuilder
    {
        return new PostgreSQLQueryBuilder;
    }
}