<?php
require("../../include/db.class.php");

$sDateStart = false;
$sDateEnd = false;
$iIdCompteur = 1;
$sType = "all";

$aRet = array();

if(!empty($_GET['dstart']) && ($iStartTime = strtotime($_GET['dstart'])) !== false){
	$aRet['dstart'] = $sDateStart = date('Y-m-d', $iStartTime).' 00:00:00';
}

if(!empty($_GET['dend']) && ($iEndTime = strtotime($_GET['dend'])) !== false){
	$aRet['dend'] = $sDateEnd = date('Y-m-d', $iEndTime).' 23:59:59';
}

if(!empty($_GET['compteur']))
	$iIdCompteur = intval($_GET['compteur']);

if(!empty($_GET['type']))
	$sType = $_GET['type'];

$aRet['type'] = $sType;
$aRet['compteur'] = $iIdCompteur;

if($sType == 'daily'){
	$sQuery =	'SELECT '.
						'UNIX_TIMESTAMP(jour) AS jour, '.
						'conso_totale, '.
						'conso_hc, '.
						'conso_hp '.
					'FROM tbl_statsjour '.
					'WHERE '.
						'id_compteur = '.intval($iIdCompteur).' '.
						(($sDateStart)?'AND jour >= '.DB::instance()->quote($sDateStart).' ':'').
						(($sDateEnd)?'AND jour <= '.DB::instance()->quote($sDateEnd).' ':'').
					'ORDER BY jour';
	$oRes = DB::instance()->query($sQuery);
	if($oRes !== false){
		$aRet['series'] = array(
			'total' => array(),
			'hp' => array(),
			'hc' => array()
		);
		foreach($oRes as $aRow){
			$aRet['series']['total'][] = array(intval($aRow['jour']), intval($aRow['conso_totale']));
			$aRet['series']['hp'][] = array(intval($aRow['jour']), intval($aRow['conso_hp']));
			$aRet['series']['hc'][] = array(intval($aRow['jour']), intval($aRow['conso_hc']));
		}
	}
	else{
		$aRet['error'] = 1;
		$aRet['errorStr'] = "DB error (".DB::instance()->errorCode()."): ".DB::instance()->errorInfo()[2];
	}
}
else{
	$sQuery =	'SELECT '.
						'UNIX_TIMESTAMP(date) AS date, '.
						'periode, '.
						'puissance_moyenne '.
					'FROM tbl_releve '.
					'WHERE '.
						'id_compteur = '.intval($iIdCompteur).' '.
						(($sDateStart)?'AND date >= '.DB::instance()->quote($sDateStart).' ':'').
						(($sDateEnd)?'AND date <= '.DB::instance()->quote($sDateEnd).' ':'').
					'ORDER BY date';
	$oRes = DB::instance()->query($sQuery);
	if($oRes !== false){
		$aRet['series'] = array(
			'base' => array(),
			'hp' => array(),
			'hc' => array()
		);
		foreach($oRes as $aRow){
			$aValue = array(intval($aRow['date']), intval($aRow['puissance_moyenne']));
			switch($aRow['periode']){
				case "HP":	$aRet['series']['hp'][] = $aValue; break;
				case "HC":	$aRet['series']['hc'][] = $aValue; break;
				default:		$aRet['series']['base'][] = $aValue; break;
			}
		}
	}
	else{
		$aRet['error'] = 1;
		$aRet['errorStr'] = "DB error (".DB::instance()->errorCode()."): ".DB::instance()->errorInfo()[2];
	}
}

header("Content-Type: application/json");
echo json_encode($aRet);
