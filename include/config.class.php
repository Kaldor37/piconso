<?php
//-----------------------------------------------------------------------
class Config {
//-----------------------------------------------------------------------
    private static $m_instance = false;
    private $oJsonConfig = false;
//-----------------------------------------------------------------------
    private function __construct(){
	$sConfigFile = dirname(__FILE__)."/../config/config.json";
	$this->m_jsonConfig = json_decode(file_get_contents($sConfigFile));
	assert($this->m_jsonConfig, 'Cannot load config file !');
    }
//-----------------------------------------------------------------------
    private function __clone(){}
//-----------------------------------------------------------------------
    public static function instance() {
        if (!(self::$m_instance instanceof self))
            self::$m_instance = new self();

        return self::$m_instance;
    }
//-----------------------------------------------------------------------
    public function __get($varName){
	if($this->m_jsonConfig && isset($this->m_jsonConfig->{$varName}))
	    return $this->m_jsonConfig->{$varName};

	return false;
    }
//-----------------------------------------------------------------------
};
//-----------------------------------------------------------------------
?>