#!/usr/bin/php
<?php
//-----------------------------------------------------------------------------
require_once("../include/config.class.php");
require_once("../include/teleinfo.class.php");
require_once("../include/logs.class.php");

$sTeleInfoFile = Config::instance()->teleinfo->file;
//-----------------------------------------------------------------------------
Logs::instance()->debug("Teleinfo extract started");

$oTeleinfo = new Teleinfo();
$iTry = 0;
do{
    if(!$oTeleinfo->load($sTeleInfoFile)){
	sleep(5);
    }
}
while(!$oTeleinfo->loaded() && ($iTry++ < 10));

if($oTeleinfo->loaded()){
    Logs::instance()->debug("Successfully loaded teleinfo after ".($iTry+1)." tries");
    if(!$oTeleinfo->save()){
	Logs::instance()->err("Error, cannot save teleinfo to DB");
	exit(1);
    }
}
else{
    Logs::instance()->err("Could not load teleinfo on '$sTeleInfoFile' after $iTry tries");
    exit(1);
}

exit(0);
//-----------------------------------------------------------------------------
?>