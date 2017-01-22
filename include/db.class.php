<?php
//-----------------------------------------------------------------------
require_once("config.class.php");
//-----------------------------------------------------------------------
class DB extends PDO {
//-----------------------------------------------------------------------
    private static $m_instance = false;
//-----------------------------------------------------------------------
    //private function __construct(){ }
//-----------------------------------------------------------------------
    private function __clone(){ }
//-----------------------------------------------------------------------
    public static function instance() {
        if (!(self::$m_instance instanceof self)){
	    $dsn =	Config::instance()->dbms->type.":".
			"dbname=".Config::instance()->dbms->db.";".
			"host=".Config::instance()->dbms->host;
            self::$m_instance = new self($dsn, Config::instance()->dbms->user, Config::instance()->dbms->pass);
	}

        return self::$m_instance;
    }
//-----------------------------------------------------------------------
};
//-----------------------------------------------------------------------
?>