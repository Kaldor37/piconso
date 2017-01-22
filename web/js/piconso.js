//------------------------------------------------------------------------------
$(document).ready(function() {
	$.ajaxSetup({cache:false});
	Highcharts.setOptions({ global: { useUTC: false } });
	
	// French translations
	Highcharts.setOptions({
		lang: {
			months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			shortMonths: ['Jan', 'Fév', 'Mars', 'Avr', 'Mai', 'Juin',  'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
			weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
			printChart: "Imprimer le graphique",
			resetZoom: "Dézoomer",
			resetZoomTitle: "Dézoomer au niveau 1:1",
			loading: "Chargement...",
			printChart: "Imprimer le graphique",
			decimalPoint: ","
		}
	});
});
//------------------------------------------------------------------------------
function toogleDateSelectionDiv(forceState){
	var open = (forceState === 'open' || $('#showDatesButtonIcon').html() == 'keyboard_arrow_down');
	if(forceState === 'close')
		open = false;

	if(open){
		$('#showDatesButtonIcon').html('keyboard_arrow_up');
		$('#dateSelectionDiv').show();
	}
	else{
		$('#showDatesButtonIcon').html('keyboard_arrow_down');
		$('#dateSelectionDiv').hide();
	}
}
//------------------------------------------------------------------------------
function switchRange(sRange){
	window.location.href = "?page=" + sCurrentPage + "&range=" + sRange;
}
//------------------------------------------------------------------------------
function switchDates(){
	var href = "?page=" + sCurrentPage;
	if(sStartDate !== null)
		href += "&dstart=" + sStartDate;
	if(sEndDate !== null)
		href += "&dend=" + sEndDate;
	window.location.href = href;
}
//------------------------------------------------------------------------------
function initConsoChart(consoChartDivId){
	$('#' + consoChartDivId).highcharts({
		chart: {
			type: 'column',
			zoomType: 'x'
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0,
				groupPadding: 0
			}
		},
		title: {
			text: 'Consommation électrique'
		},
		subtitle: {
			text: 'Tous les relevés',
		},
		xAxis: {
			type: 'datetime',
			title: {
				text: 'Relevé'
			}
		},
		yAxis: {
			title: {
				text: 'Puissance moyenne (W)'
			}
		},
		series: [
			{
				type: 'column',
				name: 'Heures creuses',
				data: []
			},
			{
				type: 'column',
				name: 'Heures pleines',
				data: []
			}
		]
	});
}
//------------------------------------------------------------------------------
function initDailyChart(dailyChartDivId){
	$('#' + dailyChartDivId).highcharts({
		chart: {
			type: 'column',
			zoomType: 'x'
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0,
				groupPadding: 0
			}
		},
		title: {
			text: 'Consommation électrique'
		},
		subtitle: {
			text: 'Statistiques par jour',
		},
		xAxis: {
			type: 'datetime',
			title: {
				text: 'Relevé'
			}
		},
		yAxis: {
			title: {
				text: 'Consommation (Wh)'
			}
		},
		series: [
			{
				type: 'column',
				name: 'Heures creuses',
				data: []
			},
			{
				type: 'column',
				name: 'Heures pleines',
				data: []
			}
		]
	});
}
//------------------------------------------------------------------------------
function initCurrentGauge(currentGaugeDivId, maxCurrent){
	$('#' + currentGaugeDivId).highcharts({
		chart: {
			type: 'gauge',
			plotBackgroundColor: null,
			plotBackgroundImage: null,
			plotBorderWidth: 0,
			plotShadow: false
		},

		title: {
			text: 'Intensité'
		},

		pane: {
			startAngle: -150,
			endAngle: 150,
			background: [
				{
					backgroundColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
						stops: [ [0, '#FFF'], [1, '#333'] ]
					},
					borderWidth: 0,
					outerRadius: '109%'
				},
				{
					backgroundColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
						stops: [ [0, '#333'], [1, '#FFF'] ]
					},
					borderWidth: 1,
					outerRadius: '107%'
				},
				{
				// default background
				},
				{
					backgroundColor: '#DDD',
					borderWidth: 0,
					outerRadius: '105%',
					innerRadius: '103%'
				}
			]
		},

		// the value axis
		yAxis: {
			min: 0,
			max: maxCurrent,

			minorTickInterval: 'auto',
			minorTickWidth: 1,
			minorTickLength: 10,
			minorTickPosition: 'inside',
			minorTickColor: '#666',

			tickPixelInterval: 35,
			tickWidth: 2,
			tickPosition: 'inside',
			tickLength: 10,
			tickColor: '#666',
			labels: {
				step: 2,
				rotation: 'auto'
			},
			title: {
				text: 'Ampères'
			},
			plotBands: [
				{
					from: 0,
					to: parseInt(maxCurrent/4),
					color: '#55BF3B' // green
				},
				,
				{
					from: parseInt(maxCurrent/4),
					to: parseInt((2*maxCurrent)/4),
					color: '#DDDF0D' // yellow
				},
				{
					from: parseInt((2*maxCurrent)/4),
					to: parseInt((3*maxCurrent)/4),
					color: '#CF7318' // orange
				},
				{
					from: parseInt((3*maxCurrent)/4),
					to: maxCurrent,
					color: '#F50031' // Red
				}
			]
		},

		series: [
			{
				name: 'Intensité',
				data: [ 0 ],
				tooltip: {
					valueSuffix: 'A'
				}
			}
		]
	});
}
//------------------------------------------------------------------------------
function initPowerGauge(powerGaugeDivId, maxPower){
	$('#' + powerGaugeDivId).highcharts({
		chart: {
			type: 'gauge',
			plotBackgroundColor: null,
			plotBackgroundImage: null,
			plotBorderWidth: 0,
			plotShadow: false
		},

		title: {
			text: 'Puissance apparente'
		},

		pane: {
			startAngle: -150,
			endAngle: 150,
			background: [
				{
					backgroundColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
						stops: [ [0, '#FFF'], [1, '#333'] ]
					},
					borderWidth: 0,
					outerRadius: '109%'
				},
				{
					backgroundColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
						stops: [ [0, '#333'], [1, '#FFF'] ]
					},
					borderWidth: 1,
					outerRadius: '107%'
				},
				{
				// default background
				},
				{
					backgroundColor: '#DDD',
					borderWidth: 0,
					outerRadius: '105%',
					innerRadius: '103%'
				}
			]
		},

		// the value axis
		yAxis: {
			min: 0,
			max: maxPower,

			minorTickInterval: 'auto',
			minorTickWidth: 1,
			minorTickLength: 10,
			minorTickPosition: 'inside',
			minorTickColor: '#666',

			tickPixelInterval: 35,
			tickWidth: 2,
			tickPosition: 'inside',
			tickLength: 10,
			tickColor: '#666',
			labels: {
				step: 2,
				rotation: 'auto'
			},
			title: {
				text: 'VA'
			},
			plotBands: [
				{
					from: 0,
					to: parseInt(maxPower/4),
					color: '#55BF3B' // green
				},
				,
				{
					from: parseInt(maxPower/4),
					to: parseInt((2*maxPower)/4),
					color: '#DDDF0D' // yellow
				},
				{
					from: parseInt((2*maxPower)/4),
					to: parseInt((3*maxPower)/4),
					color: '#CF7318' // orange
				},
				{
					from: parseInt((3*maxPower)/4),
					to: maxPower,
					color: '#F50031' // Red
				}
			]
		},

		series: [
			{
				name: 'Puissance apparente',
				data: [ 0 ],
				tooltip: {
					valueSuffix: 'VA'
				}
			}
		]
	});
}
//------------------------------------------------------------------------------
function setConsoChart(consoChartDivId, secsBetweenUpdates, queryParams){
	updateConsoChart(consoChartDivId, queryParams);
	setInterval(function(){ updateConsoChart(consoChartDivId, queryParams); }, secsBetweenUpdates*1000);
}
//------------------------------------------------------------------------------
function setDailyChart(dailyChartDivId, secsBetweenUpdates, queryParams){
	updateDailyChart(dailyChartDivId, queryParams);
	setInterval(function(){ updateDailyChart(dailyChartDivId, queryParams); }, secsBetweenUpdates*1000);
}
//------------------------------------------------------------------------------
function setDashBoard(currentGaugeDivId, powerGaugeDivId, secsBetweenUpdates){
	updateDashBoard(currentGaugeDivId, powerGaugeDivId);
	setInterval(function(){
		updateDashBoard(currentGaugeDivId, powerGaugeDivId);
	}, secsBetweenUpdates*1000);
}
//------------------------------------------------------------------------------
function updateBarChartSerie(chartSerie, newSerieData){
	var chartSerieData = chartSerie.data;

	for(var i in newSerieData){
		var msTimestamp = (newSerieData[i][0] * 1000);
		var value = newSerieData[i][1];
		var foundInChart = false;
		for(var j in chartSerieData){
			if(chartSerieData[j].x == msTimestamp){
				foundInChart = true;
			}
		}
		if(!foundInChart){
			chartSerie.addPoint([msTimestamp, value], false);
		}
	}
}
//------------------------------------------------------------------------------
function updateConsoChart(consoChartDivId, queryParams){
	$.getJSON('json/chartdata.php?type=all&' + queryParams, function(data) {
		if(data.error != undefined && data.error > 0){
			console.log('Data error : ' + data.errorStr);
			return;
		}

		if($('#' + consoChartDivId).highcharts() == undefined)
			initConsoChart(consoChartDivId);

		var consoChart = $('#' + consoChartDivId).highcharts();

		if(consoChart.series[0] && data.series['hc'])
			updateBarChartSerie(consoChart.series[0], data.series['hc']);

		if(consoChart.series[1] && data.series['hp'])
			updateBarChartSerie(consoChart.series[1], data.series['hp']);

		consoChart.redraw();
	});
}
//------------------------------------------------------------------------------
function updateDailyChart(dailyChartDivId, queryParams){
	$.getJSON('json/chartdata.php?type=daily&' + queryParams, function(data) {
		if(data.error != undefined && data.error > 0){
			console.log('Data error : ' + data.errorStr);
			return;
		}

		if($('#' + dailyChartDivId).highcharts() == undefined)
			initDailyChart(dailyChartDivId);

		var dailyChart = $('#' + dailyChartDivId).highcharts();

		if(dailyChart.series[0] && data.series['hc'])
			updateBarChartSerie(dailyChart.series[0], data.series['hc']);

		if(dailyChart.series[1] && data.series['hp'])
			updateBarChartSerie(dailyChart.series[1], data.series['hp']);

		dailyChart.redraw();
	});
}
//------------------------------------------------------------------------------
function updateDashBoard(currentGaugeDivId, powerGaugeDivId){
	$.getJSON('json/teleinfo.php', function(data) {
		if(data.error != undefined && data.error > 0){
			console.log('Data error : ' + data.errorStr);
			return;
		}

		if($('#' + currentGaugeDivId).highcharts() == undefined)
			initCurrentGauge(currentGaugeDivId, data.teleInfo.intensiteSouscrite);
			
		if($('#' + powerGaugeDivId).highcharts() == undefined)
			initPowerGauge(powerGaugeDivId, data.teleInfo.intensiteSouscrite*230);

		var currentGauge = $('#' + currentGaugeDivId).highcharts();
		if(currentGauge.series[0].points[0] && data.teleInfo.intensiteInstantanee){
			currentGauge.series[0].points[0].update(data.teleInfo.intensiteInstantanee);
		}

		var powerGauge = $('#' + powerGaugeDivId).highcharts();
		if(powerGauge.series[0].points[0] && data.teleInfo.puissanceApparente){
			powerGauge.series[0].points[0].update(data.teleInfo.puissanceApparente);
		}

		$('#idCurrentPeriode').html(data.teleInfo.periodeTarifaireEnCours);
		$('#idIndexHP').html(parseInt(data.teleInfo.indexConso.HP/1000));
		$('#idIndexHC').html(parseInt(data.teleInfo.indexConso.HC/1000));
		
		$('#idDashBoardInfosLoading').hide();
		$('#idDashBoardInfos').show();
	});
}
//------------------------------------------------------------------------------
