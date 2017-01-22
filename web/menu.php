<?php
class Menu {
	const PAGE_CHART_ALL		= 'chart/all';
	const PAGE_CHART_DAILY	= 'chart/daily';
	const PAGE_DASHBOARD		= 'dashboard';

	private $sPage = NULL;
	private $sChartRange = NULL;
	private $aChartParams = NULL;

	public function __construct(){
		$this->sPage = self::PAGE_CHART_ALL;
		if(isset($_GET['page']) && !empty($_GET['page']))
			$this->sPage = $_GET['page'];

		if($this->sPage === self::PAGE_CHART_ALL || $this->sPage === self::PAGE_CHART_DAILY){
			$this->aChartParams = array();

			if(isset($_GET['dstart']) && !empty($_GET['dstart'])){
				$dStart = DateTime::createFromFormat('Y-m-d', $_GET['dstart']);
				if($dStart !== false)
					$this->aChartParams['dstart'] = $dStart->format('Y-m-d');
			}
			if(isset($_GET['dend']) && !empty($_GET['dend'])){
				$dEnd = DateTime::createFromFormat('Y-m-d', $_GET['dend']);
				if($dEnd !== false)
					$this->aChartParams['dend'] = $dEnd->format('Y-m-d');
			}

			if(empty($this->aChartParams)){
				if(isset($_GET['range']) && !empty($_GET['range']))
					 $this->sChartRange = $_GET['range'];

				switch($this->sChartRange){
					case 'yesterday':{
						$sYesterday = date('Y-m-d', strtotime('yesterday'));
						$this->aChartParams['dstart'] = $sYesterday;
						$this->aChartParams['dend'] = $sYesterday;
					}break;

					case 'currweek':{
						$this->aChartParams['dstart'] = date('Y-m-d', strtotime('monday this week'));
					}break;

					case 'lastweek':{
						$this->aChartParams['dstart'] = date('Y-m-d', strtotime('monday last week'));
						$this->aChartParams['dend'] = date('Y-m-d', strtotime('sunday last week'));
					}break;

					case 'currmonth':{
						if(date('d') === "01")
							$this->aChartParams['dstart'] = date('Y-m-d', strtotime('first day of last month'));
						else
							$this->aChartParams['dstart'] = date('Y-m').'-01';
					}break;

					case 'lastmonth':{
						$this->aChartParams['dstart'] = date('Y-m-d', strtotime('first day of last month'));
						$this->aChartParams['dend'] = date('Y-m-d', strtotime('last day of last month'));
					}break;
				
					case 'all': break;

					default:
					case 'today':
						$this->aChartParams['dstart'] = date('Y-m-d');
					break;
				}
			}
		}
	}
	
	public function page(){
		return $this->sPage;
	}

	public function chartRange(){
		return $this->sChartRange;
	}

	public function chartParams($sKey=""){
		if($sKey != ''){
			return isset($this->aChartParams[$sKey])?$this->aChartParams[$sKey]:null;
		}
		return $this->aChartParams;
	}

	public function active($sPage, $sRange=""){
		return (($sPage === $this->sPage) && (empty($sRange) || $sRange === $this->sChartRange));
	}
}
