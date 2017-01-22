<?php
//-----------------------------------------------------------------------
require_once("config.class.php");
//-----------------------------------------------------------------------
class Logs {
//-----------------------------------------------------------------------
    private static $m_instance = false;

    private $m_opened = false;
    private $m_minPriority = LOG_NOTICE;
//-----------------------------------------------------------------------
    private function __construct(){
	$sFacility = Config::instance()->logs->facility;
	if($sFacility === false)
	    return;

	$sIdent = "";
	if(isset(Config::instance()->logs->ident))
	    $sIdent = Config::instance()->logs->ident;

	if(isset(Config::instance()->logs->priority)){
	    $sPriorityConst = 'LOG_'.strtoupper(Config::instance()->logs->priority);
	    if(defined($sPriorityConst))
		$this->m_minPriority = constant($sPriorityConst);
	}

	$sFacilityConst = 'LOG_'.strtoupper($sFacility);
	if(!defined($sFacilityConst))
	    return;

	$this->m_opened = openlog($sIdent, LOG_PID, constant($sFacilityConst));
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
    public function __call($sName, $aArgs){
	assert($this->m_opened);
	if(!$this->m_opened)
	    return false;

	if(!is_string($sName) || empty($sName))
	    return false;

	$sPriorityConst = 'LOG_'.strtoupper($sName);
	if(!defined($sPriorityConst))
	    return false;

	if(!isset($aArgs[0]) || !is_string($aArgs[0]) || empty($aArgs[0]))
	    return false;

	$iPriority = constant($sPriorityConst);
	if($iPriority > $this->m_minPriority)
	    return false;

	return syslog($iPriority, trim($aArgs[0]));
    }
//-----------------------------------------------------------------------
};
//-----------------------------------------------------------------------
?>