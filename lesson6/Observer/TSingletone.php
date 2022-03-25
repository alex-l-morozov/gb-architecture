<?php

/**
 * trait TSingletone
 */
trait TSingletone
{
    /**
     * @var $instance
     */
    private static  $instance;

    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    /**
     * @return static
     */
    public static function getInstance(){
        return static::$instance ?? static::$instance = new self();
    }
}
