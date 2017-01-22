#!/usr/bin/php
<?php
//-----------------------------------------------------------------------------
require_once("../include/config.class.php");
require_once("../include/logs.class.php");
require_once("../include/db.class.php");
//-----------------------------------------------------------------------------
// Last 'releve' for a day and a compteur
function lastReleveOf($oDate, $iIdCompteur){
    $oRes = DB::instance()->query(
	'SELECT * '.
	'FROM tbl_releve '.
	'WHERE '.
	    'date BETWEEN '.
		DB::instance()->quote($oDate->format('Y-m-d').' 00:00:00').' '.
		'AND '.DB::instance()->quote($oDate->format('Y-m-d').' 23:59:59').' '.
	    'AND id_compteur = '.intval($iIdCompteur).' '.
	'ORDER BY date DESC '.
	'LIMIT 1'
    );
    if($oRes !== false){
	if($oRes->rowCount() > 0){
    	    $aRow = $oRes->fetch(PDO::FETCH_ASSOC);
	    if(!empty($aRow))
		return $aRow;
        }
    }

    return false;
}
//-----------------------------------------------------------------------------
function calcDailyStatsFor($oDate, $iIdCompteur){
    $oDateLast = clone $oDate;
    $oDateLastLast = clone $oDate;
    $oDateLastLast = $oDateLastLast->sub(new DateInterval('P1D'));

    // Get last releve of this day
    $aLastReleve = lastReleveOf($oDateLast, $iIdCompteur);
    // And last of the previous day
    $aLastLastReleve = lastReleveOf($oDateLastLast, $iIdCompteur);

    if(!$aLastReleve || !$aLastLastReleve)
	return false;

    $iTotalConso = bcsub($aLastReleve['index_total'], $aLastLastReleve['index_total']);
    $iHPConso = 0;
    $iHCConso = 0;
    if(!empty($aLastReleve['index_hp']))
	$iHPConso = bcsub($aLastReleve['index_hp'], $aLastLastReleve['index_hp']);
    if(!empty($aLastReleve['index_hc']))
	$iHCConso = bcsub($aLastReleve['index_hc'], $aLastLastReleve['index_hc']);

    $oRes = DB::instance()->query(
	'INSERT INTO tbl_statsjour (jour, id_compteur, conso_totale, conso_hp, conso_hc) '.
	'VALUES ('.
	    DB::instance()->quote($oDateLast->format('Y-m-d')).', '.
	    intval($iIdCompteur).', '.
	    DB::instance()->quote($iTotalConso).', '.
	    (empty($iHPConso)?'NULL':DB::instance()->quote($iHPConso)).', '.
	    (empty($iHCConso)?'NULL':DB::instance()->quote($iHCConso)).
	') '.
	'ON DUPLICATE KEY UPDATE '.
	    'conso_totale = '.DB::instance()->quote($iTotalConso).', '.
	    'conso_hp = '.(empty($iHPConso)?'NULL':DB::instance()->quote($iHPConso)).', '.
	    'conso_hc = '.(empty($iHCConso)?'NULL':DB::instance()->quote($iHCConso))
    );
    return ($oRes !== false);
}
//-----------------------------------------------------------------------------
$oDateStart = NULL;
$oDateEnd = NULL;
$oToday = new DateTime();
$iIdCompteur = 1;

$sShortOpts  = "hys:e:d:";
$aLongOpts  = array(
    "help",
    "yesterday",
    "day:",
    "start:",
    "end:"
);
$aOptions = getopt($sShortOpts, $aLongOpts);

// Help
if(isset($aOptions['h']) || isset($aOptions['help'])){
    die('TODO HELP');
}
// Stats of yesterday (default)
else if(isset($aOptions['y']) || isset($aOptions['yesterday']) || empty($aOptions)){
    $oDateStart = $oDateEnd = new DateTime('yesterday');
}
// Stats of the given day
else if(($bHasD = !empty($aOptions['d'])) || !empty($aOptions['day'])){
    $sDate = ($bHasD)?$aOptions['d']:$aOptions['day'];
    try{
	$oDateStart = $oDateEnd = new DateTime($sDate);
    }
    catch(Exception $e){
	$oDateStart = NULL;
	$oDateEnd = NULL;
    }
}
// Stats for evey day between two dates
else if(
    (($bHasS = !empty($aOptions['s'])) || !empty($aOptions['start'])) && 
    (($bHasE = !empty($aOptions['e'])) || !empty($aOptions['end']))
){
    $sDateStart = ($bHasS)?$aOptions['s']:$aOptions['start'];
    $sDateEnd = ($bHasE)?$aOptions['e']:$aOptions['end'];
    try{
	$oDateStart = new DateTime($sDateStart);
	$oDateEnd = new DateTime($sDateEnd);
    }
    catch(Exception $e){
	$oDateStart = NULL;
	$oDateEnd = NULL;
    }
}

if(!is_object($oDateStart) || !is_object($oDateEnd) || $oDateEnd < $oDateStart || $oDateEnd >= $oToday){
    echo 'Invalid dates !'.PHP_EOL;
    exit(1);
}


$oItDate = clone $oDateStart;
while($oItDate <= $oDateEnd){
    $sDate = $oItDate->format('Y-m-d');
    if(calcDailyStatsFor($oItDate, $iIdCompteur)){
	Logs::instance()->debug('Successfully computed daily stats for '.$sDate);
    }
    else{
	Logs::instance()->err('Could not compute daily stats for '.$sDate);
    }
    $oItDate->add(new DateInterval('P1D'));
}

exit(0);
