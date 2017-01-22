<?php
require('../../include/config.class.php');
require('../../include/teleinfo.class.php');

$aRet = array();

$oTeleinfo = new Teleinfo();
$iTry = 0;
do{
	if(!$oTeleinfo->load(Config::instance()->teleinfo->file)){
		sleep(1);
	}
}
while(!$oTeleinfo->loaded() && (++$iTry < 5));

if($oTeleinfo->loaded()){
	$aRet['tries'] = $iTry+1;
	$aRet['teleInfo'] = $oTeleinfo->toArray();
	if(isset($aRet['teleInfo']['adresseCompteur']))
		unset($aRet['teleInfo']['adresseCompteur']);
}
else{
	$aRet['error'] = 1;
	$aRet['errorStr'] = "Cannot load teleinfo on: '$sTeleInfoFile' after $sTry tries";
}

header("Content-Type: application/json");
echo json_encode($aRet);
