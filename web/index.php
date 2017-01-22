<?php
require("menu.php");
$oMenu = new Menu();
?>
<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>PiConso</title>

		<!-- Stylesheets -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
		<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="css/piconso.css">

		<!-- Scripts -->
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery-ui.min.js"></script>
		<script src="js/highcharts.js"></script>
		<script src="js/highcharts-more.js"></script>
		<script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
		<script src="js/piconso.js"></script>
		<script language="javascript">
		var sCurrentPage = <?=json_encode($oMenu->page());?>;
		var sStartDate = <?=json_encode($oMenu->chartParams('dstart'));?>;
		var sEndDate = <?=json_encode($oMenu->chartParams('dend'));?>;

		$(document).ready(function() {
			<?php if($oMenu->page() === Menu::PAGE_CHART_DAILY): ?>
			setDailyChart('idChart', 10000, <?=json_encode(http_build_query($oMenu->chartParams()));?>);
			<?php elseif($oMenu->page() === Menu::PAGE_CHART_ALL): ?>
			setConsoChart('idChart', 600, <?=json_encode(http_build_query($oMenu->chartParams()));?>);
			<?php elseif($oMenu->page() === Menu::PAGE_DASHBOARD): ?>
			setDashBoard('idCurrentGauge', 'idPowerGauge', 10);
			<?php endif; ?>

			<?php if($oMenu->page() === Menu::PAGE_DASHBOARD): ?>
			$('#idDashBoardInfos').hide();
			<?php else: ?>
			$('#dateStartPicker').datepicker({
				dateFormat: 'yy-mm-dd',
				onSelect: function(sDate, oInstance){
					if(sStartDate != sDate){
						sStartDate = sDate;
						$('#dateStart').html(sDate);
						$('#datesApplyButton').show();
						$('#dateStart').click();
					}
				}
			});
			if(sStartDate !== null){
				$('#dateStartPicker').datepicker('setDate', sStartDate);
			}
			$('#dateEndPicker').datepicker({
				dateFormat: 'yy-mm-dd',
				onSelect: function(sDate, oInstance){
					if(sEndDate != sDate){
						sEndDate = sDate;
						$('#dateEnd').html(sDate);
						$('#datesApplyButton').show();
						$('#dateEnd').click();
					}
				}
			});
			if(sEndDate !== null){
				$('#dateEndPicker').datepicker('setDate', sEndDate);
			}
			$('#showDatesButton').click(toogleDateSelectionDiv);
			
			$('#datesApplyButton').click(switchDates);
			$('#datesApplyButton').hide();
			<?php endif; ?>
		});
		</script>
	</head>
	<body>
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
			<header class="mdl-layout__header">
				<div class="mdl-layout__header-row">
					<span class="mdl-layout-title">PiConso</span>
					<div class="mdl-layout-spacer"></div>
					<nav class="mdl-navigation mdl-layout--large-screen-only">
						<a class="mdl-navigation__link" href="?page=chart/all&range=currweek">Consommation</a>
						<a class="mdl-navigation__link" href="?page=chart/daily&range=currmonth">Par jour</a>
						<a class="mdl-navigation__link" href="?page=dashboard">Dashboard</a>
					</nav>
				<?php if($oMenu->page() == Menu::PAGE_CHART_ALL || $oMenu->page() == Menu::PAGE_CHART_DAILY): ?>
					<button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored mdl-shadow--4dp mdl-color--accent" id="showDatesButton">
						<i class="material-icons" id="showDatesButtonIcon">keyboard_arrow_down</i>
						<span class="visuallyhidden">Dates</span>
					</button>
				<?php endif; ?>
					<div class="mdl-layout-spacer" id="showDatesButtonSpacer"></div>
				</div>
			</header>
			<div class="mdl-layout__drawer">
				<span class="mdl-layout-title">PiConso</span>
				<nav class="mdl-navigation">
					<a class="mdl-navigation__link" href="?page=chart/all&range=currweek">Consommation</a>
					<a class="mdl-navigation__link" href="?page=chart/daily&range=currmonth">Par jour</a>
					<a class="mdl-navigation__link" href="?page=dashboard">Dashboard</a>
				</nav>
			</div>

			<main class="mdl-layout__content mdl-color--grey-100">
				<div class="mdl-grid">
				<?php if($oMenu->page() == Menu::PAGE_CHART_ALL || $oMenu->page() == Menu::PAGE_CHART_DAILY): ?>
					<div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col" id="dateSelectionDiv">
						<button class="mdl-button mdl-js-button mdl-button--icon" id="rangeMenuButton">
							<i class="material-icons">more_vert</i>
						</button>
						<ul class="mdl-menu mdl-menu--bottom-left mdl-js-menu mdl-js-ripple-effect" for="rangeMenuButton">
							<li class="mdl-menu__item" onclick="switchRange('today')">Aujourd'hui</li>
							<li class="mdl-menu__item" onclick="switchRange('yesterday')">Hier</li>
							<li class="mdl-menu__item" onclick="switchRange('currweek')">Semaine en cours</li>
							<li class="mdl-menu__item" onclick="switchRange('lastweek')">La semaine dernière</li>
							<li class="mdl-menu__item" onclick="switchRange('currmonth')">Mois en cours</li>
							<li class="mdl-menu__item" onclick="switchRange('lastmonth')">Le mois dernier</li>
							<?php if($oMenu->page() === Menu::PAGE_CHART_DAILY): ?>
							<li class="mdl-menu__item" onclick="switchRange('all')">Depuis le début</li>
							<?php endif; ?>
						</ul>

						<button class="mdl-button mdl-js-button datePickerButtons" id="dateStart">
							<?php if($oMenu->chartParams('dstart') !== null): ?>
							<?=$oMenu->chartParams('dstart');?>
							<?php else: ?>
							Date de début
							<?php endif; ?>
						</button>
						<div class="mdl-menu mdl-menu--bottom-left mdl-js-menu mdl-js-ripple-effect" for="dateStart" id="dateStartPicker"></div>

						<button class="mdl-button mdl-js-button datePickerButtons" id="dateEnd">
							<?php if($oMenu->chartParams('dend') !== null): ?>
							<?=$oMenu->chartParams('dend');?>
							<?php else: ?>
							Date de fin
							<?php endif; ?>
						</button>
						<div class="mdl-menu mdl-menu--bottom-left mdl-js-menu mdl-js-ripple-effect" for="dateEnd" id="dateEndPicker"></div>
						
						<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" id="datesApplyButton">
							<i class="material-icons">done</i>
						</button>
					</div>

					<div class="consoChart mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col mdl-grid" id="idChart">
						<div class="mdl-spinner mdl-js-spinner mdl-spinner--single-color is-active loadingSpinner"></div>
					</div>
				<?php elseif($oMenu->page() == Menu::PAGE_DASHBOARD): ?>
					<div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-grid dashBoardCard" id="idPowerGauge">
						<div class="mdl-spinner mdl-js-spinner mdl-spinner--single-color is-active loadingSpinner"></div>
					</div>
					<div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-grid dashBoardCard" id="idCurrentGauge">
						<div class="mdl-spinner mdl-js-spinner mdl-spinner--single-color is-active loadingSpinner"></div>
					</div>
					<div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-grid dashBoardCard">
						<div id="idDashBoardInfos">
							<h4>Informations</h4>
							<ul>
								<li>Période en cours : <span id="idCurrentPeriode"></span></li>
								<li>Index heures pleines : <span id="idIndexHP">0</span> kWh</li>
								<li>Index heures creuses : <span id="idIndexHC">0</span> kWh</li>
							</ul>
						</div>
						<div class="mdl-spinner mdl-js-spinner mdl-spinner--single-color is-active loadingSpinner" id="idDashBoardInfosLoading"></div>
					</div>
				<?php endif; ?>

				</div>
			</main>
		</div>
	</body>
</html>
