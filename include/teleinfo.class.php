<?php
//-----------------------------------------------------------------------
require_once("db.class.php");
require_once("logs.class.php");
//-----------------------------------------------------------------------
class TeleInfo {
//-----------------------------------------------------------------------
// Members
//-----------------------------------------------------------------------
    private $m_aData = NULL;

//-----------------------------------------------------------------------
// Constructor
//-----------------------------------------------------------------------
    public function __construct($sInputFile=""){
	if(!empty($sInputFile))
	    $this->load($sInputFile);
    }

//-----------------------------------------------------------------------
// Public functions
//-----------------------------------------------------------------------
    public function load($sInputFile){
	$rFile = fopen($sInputFile, "r");
	if($rFile === false){
	    Logs::instance()->err("TeleInfo::Load - Cannot open $sInputFile for reading !");
	    return false;
	}

	$cStart = chr(2);
	$bReading = true;
	$bExtracting = false;
	$sBuffer = '';
	$cChar = '';

	do{
	    $cChar = fread($rFile, 1);
	    if($cChar === false){
		Logs::instance()->err("TeleInfo::Load - Failed reading in $sInputFile !");
		fclose($rFile);
		return false;
	    }

	    // We are extracting data
	    if($bExtracting){
	        // Stop extracting, we reached the end of the data
	        if($cChar == $cStart){ $bReading = false; }
		else{ $sBuffer .= $cChar; }
	    }
	    // Start extracting
	    else if($cChar == $cStart){ $bExtracting = true; }
	}
	while($bReading);

	fclose($rFile);

	$this->m_aData = array();
	$aTmpTeleInfo = explode("\n", $sBuffer);
	foreach($aTmpTeleInfo as $iKey => $sLine){
	    if(empty($sLine))
		continue;

	    if(preg_match("/([A-Z]*) ([A-Z0-9.]*) (.).*/", $sLine, $aMatches) === 1){
		$this->m_aData[$aMatches[1]] = $aMatches[2];
	    }
	    else{
		Logs::instance()->err("TeleInfo::Load - Extract pattern didn't match '$sLine' in : '".implode('|', $aTmpTeleInfo)."'");
		$this->m_aData = NULL;
		return false;
	    }
	}
	
	if(!empty($this->m_aData))
	    return true;
	else
	    $this->m_aData = NULL;

	return false;
    }

//-----------------------------------------------------------------------
// Accessors
//-----------------------------------------------------------------------
    public function loaded(){
	return ($this->m_aData !== NULL);
    }
//-----------------------------------------------------------------------
    public function __get($sTagName){
	if(isset($this->m_aData[$sTagName]))
	    return $this->m_aData[$sTagName];

	return false;
    }
//-----------------------------------------------------------------------
    public function adresseCompteur(){
	if(!$this->loaded())
	    return false;

	if(isset($this->m_aData['ADCO'])){
	    return $this->m_aData['ADCO'];
	}
	return false;
    }
//-----------------------------------------------------------------------
    public function intensiteSouscrite(){
	if(!$this->loaded())
	    return false;

	if(isset($this->m_aData['ISOUSC'])){
	    return intval($this->m_aData['ISOUSC']);
	}
	return false;
    }
//-----------------------------------------------------------------------
    public function optionTarifaire(){
	if(!$this->loaded())
	    return false;

	if(isset($this->m_aData['OPTARIF'])){
	    return rtrim($this->m_aData['OPTARIF'], '.');
	}
	return false;
    }
//-----------------------------------------------------------------------
    public function indexConso(){
	if(!$this->loaded())
	    return false;

	$mRet = false;

	$opt = $this->optionTarifaire();
	switch($opt){
	    case 'HC':
		if(isset($this->m_aData['HCHP']) && isset($this->m_aData['HCHC'])){
		    $mRet = array(
			"HP" => intval($this->m_aData['HCHP']),
			"HC" => intval($this->m_aData['HCHC'])
		    );
		}
	    break;

	    case 'BASE':
		if(isset($this->m_aData['BASE'])){
		    $mRet = intval($this->m_aData['BASE']);
		}
	    break;

	    default: echo "Option tarifaire '$opt' inconnue (ou non gérée)\n";
	}

	return $mRet;
    }
//-----------------------------------------------------------------------
    public function periodeTarifaireEnCours(){
	if(!$this->loaded())
	    return false;

	if(isset($this->m_aData['PTEC'])){
	    return rtrim($this->m_aData['PTEC'], '.');
	}
	return false;
    }
//-----------------------------------------------------------------------
    public function intensiteMax(){
	if(!$this->loaded())
	    return false;

	if(isset($this->m_aData['IMAX'])){
	    return intval($this->m_aData['IMAX']);
	}
	return false;
    }
//-----------------------------------------------------------------------
    public function intensiteInstantanee(){
	if(!$this->loaded())
	    return false;

	if(isset($this->m_aData['IINST'])){
	    return intval($this->m_aData['IINST']);
	}
	return false;
    }
//-----------------------------------------------------------------------
    public function puissanceApparente(){
	if(!$this->loaded())
	    return false;

	if(isset($this->m_aData['PAPP'])){
	    return intval($this->m_aData['PAPP']);
	}
	return false;
    }
//-----------------------------------------------------------------------
    public function save(){
	// Id du compteur
	$oRes = DB::instance()->query(
	    "SELECT id ".
	    "FROM tbl_compteur ".
	    "WHERE adresse = ".DB::instance()->quote($this->adresseCompteur())
	);
	$iIdCompteur = 0;
	if($oRes->rowCount() > 0){
	    $aRow = $oRes->fetch(PDO::FETCH_ASSOC);
	    $iIdCompteur = intval($aRow['id']);
	}
	if($iIdCompteur <= 0){
	    // TODO - Create compteur
	    return false;
	}

	// Dernier relevé
	$oRes = DB::instance()->query(
	    "SELECT ".
		"index_total, ".
		"UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(date) AS time_diff ".
	    "FROM tbl_releve ".
	    "WHERE id_compteur = ".intval($iIdCompteur)." ".
	    "ORDER BY id DESC ".
	    "LIMIT 1"
	);
	$iLastIndex = false;
	$iTimeDiff = 0;
	if($oRes !== false){
	    if($oRes->rowCount() > 0){
		$aRow = $oRes->fetch(PDO::FETCH_ASSOC);
		$iLastIndex = intval($aRow['index_total']);
		$iTimeDiff = intval($aRow['time_diff']);
	    }
	}
	else{
	    
	    return false;
	}

	$iIndexTotal = 0;
	$iIndexHC = false;
	$iIndexHP = false;
	$iDiffConso = 0;
	$iPMoy = 0;
	if($this->optionTarifaire() == "HC"){
	    $aIndex = $this->indexConso();
	    $iIndexHP = intval($aIndex['HP']);
	    $iIndexHC = intval($aIndex['HC']);
	    $iIndexTotal = ($iIndexHP + $iIndexHC);

	    if($iLastIndex !== false && $iLastIndex > 0 && $iTimeDiff > 0)
		$iDiffConso = $iIndexTotal - $iLastIndex;
	    if($iDiffConso > 0 && $iTimeDiff > 0)
		$iPMoy = intval(($iDiffConso*3600)/$iTimeDiff);
	}
	else{
	    // TODO
	    return false;
	}

	$oInsQry = DB::instance()->prepare(
	    "INSERT INTO tbl_releve (".
		"date, id_compteur, periode, ".
		"index_total, index_hp, index_hc, ".
		"puissance_moyenne".
	    ") ".
	    "VALUES (NOW(), ?, ?, ?, ?, ?, ?)"
	);
	$aParams = array(
	    $iIdCompteur, $this->periodeTarifaireEnCours(),
	    $iIndexTotal, $iIndexHP, $iIndexHC, $iPMoy
	);
	return $oInsQry->execute($aParams);
    }
//-----------------------------------------------------------------------
    function toArray(){
	if(!$this->loaded())
	    return false;

	return array(
	    "adresseCompteur"		=> $this->adresseCompteur(),
	    "intensiteSouscrite"	=> $this->intensiteSouscrite(),
	    "optionTarifaire"		=> $this->optionTarifaire(),
	    "indexConso"		=> $this->indexConso(),
	    "periodeTarifaireEnCours"	=> $this->periodeTarifaireEnCours(),
	    "intensiteMax"		=> $this->intensiteMax(),
	    "intensiteInstantanee"	=> $this->intensiteInstantanee(),
	    "puissanceApparente"	=> $this->puissanceApparente()
	);
    }
//-----------------------------------------------------------------------
}
//-----------------------------------------------------------------------
?>