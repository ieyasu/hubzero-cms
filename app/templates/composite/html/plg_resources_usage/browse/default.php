<?php
/**
 * @purpose  Add extra functionality, such as downloads, to charts. Requires replacing Flot with Highchart
 */

// no direct access
defined('_HZEXEC_') or die('Restricted access');

// Set date time format
$dateFormat = 'M Y'; //'d M Y';
$tz = true;

// Push scripts to document
\Hubzero\Document\Assets::addPluginStylesheet('resources', 'usage');

Document::addScript('/app/templates/NaN/html/plg_resources_usage/browse/highstock.js');
Document::addScript('/app/templates/NaN/html/plg_resources_usage/browse/modules/exporting.src.js');

// Set the base URL
if ($this->resource->alias) {
	$url = 'index.php?option=' . $this->option . '&alias=' . $this->resource->alias . '&active=usage';
} else {
	$url = 'index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=usage';
}

$img1 = $this->chart_path . $this->dthis . '-' . $this->period . '-' . $this->resource->id . '-Users.gif';
$img2 = $this->chart_path . $this->dthis . '-' . $this->period . '-' . $this->resource->id . '-Jobs.gif';

$cls = 'even';

$database = App::get('db');

$topvals = new \Components\Resources\Tables\Stats\Tools\Topvals($database);

switch ($this->params->get('defaultDataset', 'cumulative'))
{
	case 'yearly': $prd = 12; break;
	case 'monthly': $prd = 1; break;
	case 'cumulative': 
	default: $prd = 14; break;
}

if (intval($this->params->get('cache', 1)))
{
	if (!($results = Cache::get('resources.usage' . $this->resource->id . 'overview')))
	{
		$results = plgResourcesUsage::getOverview($this->resource->id, $prd);

		Cache::put('resources.usage' . $this->resource->id . 'overview', $results, intval($this->params->get('cache_time', 15)));
	}
}
else 
{
	$results = plgResourcesUsage::getOverview($this->resource->id, $prd);
}

$users = array();
$interactive = array();
$sessions = array();
$runs = array();

//$usersScaled = array();
//$runsScaled = array();

$min = (date("Y") - 1) . '/' . date("m") . '/01';
$to = $max = date("Y") . '/' . date("m") . '/01';
$from = (date("Y") - 1) . '/' . date("m") . '/01';
$half = date('Y/m/d', mktime(0, 0, 0, (date("m") - 6), 1, date("Y")));
$qrtr = date('Y/m/d', mktime(0, 0, 0, (date("m") - 3), 1, date("Y")));

if ($results)
{
	$usersTop = 0;
	$runsTop = 0;

	//$c = count($results);
	foreach ($results as $result)
	{
		$users[]       = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->users . "]";
		$interactive[] = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->sessions . "]";
		$sessions[]    = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->simulations . "]";
		$runs[]        = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->jobs . "]";

		$usersTop = ($result->users > $usersTop) ? $result->users : $usersTop;
		$runsTop = ($result->jobs > $runsTop) ? $result->jobs : $runsTop;
	}

	//$min = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $results[0]->datetime));
}

$current = end($results);
?>
	<h3 id="plg-usage-header">
		<a name="usage"></a>
		<?php echo Lang::txt('PLG_RESOURCES_USAGE'); ?>
	</h3>
	<form method="get" action="<?php echo Route::url($url); ?>">
		<input type="hidden" name="period" id="period" value="<?php echo $prd; ?>" />
	<?php
	$usageConfig = Component::params('com_usage');
	$tool_map = substr(PATH_APP, strlen(PATH_ROOT)) . '/site/stats/resource_maps/' . $this->resource->id;
	if (file_exists(PATH_ROOT . $tool_map . '.gif') || file_exists(PATH_ROOT . $tool_map . '.xml')) : ?>
		<div id="geo-overview-wrap" class="usage-wrap">
			<div class="four columns first">
				<h4><?php echo Lang::txt('World usage'); ?></h4>
				<p><?php echo Lang::txt('PLG_RESOURCES_USAGE_MAP_EXPLANATION', stripslashes($this->resource->title)); ?></p>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
			<?php if (file_exists(PATH_ROOT . $tool_map . '.xml')) { ?>
				<div id="div_map" style="width:100%; height:300px"></div>
				<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $usageConfig->get('mapsApiKey'); ?>&amp;sensor=false"> </script>
				<script type="text/javascript" src="/app/templates/NaN/html/plg_resources_usage/browse/util.js"> </script>
				<script type="text/javascript">
				if (!jq) {
					var jq = $;
				}
				if (jQuery()) {
					var $ = jq;

					var infowindow, map;

					function initMap() {
						var myLatlng = new google.maps.LatLng(<?php echo $this->params->get('mapLat', 20); ?>,<?php echo $this->params->get('mapLong', 0); ?>);
						var myOptions = {
							zoom: <?php echo $this->params->get('mapZoom', 2); ?>,
							center: myLatlng,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						}
						map = new google.maps.Map(document.getElementById("div_map"), myOptions);

						var urlstr="<?php echo rtrim(Request::base(), '/') . $tool_map; ?>.xml";

						downloadUrl(urlstr, function(data) {
							var markers = data.documentElement.getElementsByTagName("marker");
							for (var i = 0; i < markers.length; i++) {
								var latlng = new google.maps.LatLng(parseFloat(markers[i].getAttribute("lat")), parseFloat(markers[i].getAttribute("lng")));
								var marker = createMarker(markers[i].getAttribute("info"), latlng);
							}
						});
					}

					function createMarker(info, latlng) {
						if (info) {
							info = info.replace(/_br_/gi,'<br />');
							info = info.replace(/_hr_/gi,'<hr />');
							info = info.replace(/_b_/gi,'<b>');
							info = info.replace(/_bb_/gi,'</b>');
						}
							var marker = new google.maps.Marker({position: latlng, map: map});
							google.maps.event.addListener(marker, "click", function() {
								if (infowindow) infowindow.close();
								infowindow = new google.maps.InfoWindow({content: info});
								infowindow.open(map, marker);
							});
							return marker;
						
					}

					$(document).ready(function() {
						initMap()
					});
				}
				</script>
			<?php } else if (file_exists(PATH_ROOT . $tool_map . '.gif')) { ?>
				<p>
					<a href="<?php echo $tool_map; ?>.png" title="<?php echo Lang::txt('PLG_RESOURCES_USAGE_MAP_LARGER'); ?>">
						<img style="width:100%;max-width:510px;" src="<?php echo $tool_map; ?>.gif" alt="<?php echo Lang::txt('PLG_RESOURCES_USAGE_MAP'); ?>" />
					</a>
				</p>
			<?php } ?>
			</div><!-- / .four columns second third fourth -->
			<div style="clear:left;"></div>
		</div>
	<?php endif; ?>

		<div id="user-overview-wrap" class="usage-wrap">
			<ul class="dataset-controls" id="set-data">
				<li>
					<a id="monthly" class="dataset<?php if ($this->params->get('defaultDataset', 'cumulative') == 'monthly') { echo ' active'; } ?>" href="/index.php?option=com_resources&amp;id=<?php echo $this->resource->id; ?>&amp;active=usage&amp;action=overview&amp;period=1">
						Monthly
					</a>
				</li>
				<li>
					<a id="yearly" class="dataset<?php if ($this->params->get('defaultDataset', 'cumulative') == 'yearly') { echo ' active'; } ?>" href="/index.php?option=com_resources&amp;id=<?php echo $this->resource->id; ?>&amp;active=usage&amp;action=overview&amp;period=12">
						Yearly
					</a>
				</li>
				<li>
					<a id="cumulative" class="dataset<?php if ($this->params->get('defaultDataset', 'cumulative') == 'cumulative') { echo ' active'; } ?>" href="/index.php?option=com_resources&amp;id=<?php echo $this->resource->id; ?>&amp;active=usage&amp;action=overview&amp;period=14">
						Cumulative
					</a>
				</li>
			</ul>
			<div class="four columns first">
				<h4><?php echo Lang::txt('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?></h4>
				<p class="total">
					<strong id="users-overview-total"><?php echo number_format($current->users); ?></strong>
					<span id="users-overview-date"><time datetime="<?php echo $result->datetime; ?>"><?php echo Date::of($result->datetime)->toLocal($dateFormat); ?></time></span></span>
				</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<div id="users-overview" style="min-width:400px;height:250px;">
				<?php
				if ($results)
				{
					// Find the highest value
					$vals = array();
					foreach ($results as $result)
					{
						$vals[] = $result->users;
					}
					asort($vals);

					$highest = array_pop($vals);

					$sparkline  = '<span class="sparkline">' . "\n";
					foreach ($results as $result)
					{
						$height = round(($result->users / $highest)*100);
						$sparkline .= "\t" . '<span class="index">';
						$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="' . Date::of($result->datetime)->toLocal($dateFormat) . ': ' . number_format($result->users) . '">';
						$sparkline .= number_format($result->users); //trim($this->_fmt_result($result->value, $result->valfmt));
						$sparkline .= '</span> ';
						$sparkline .= '</span>' . "\n";
					}
					$sparkline .= '</span>' . "\n";
					echo $sparkline;
				} 
				?>
				</div>
			

		<script type="text/javascript">
	if (!jq) {
		var jq = $;
	}
	if (jQuery()) {
		var $ = jq;
		
		dataurl = '/index.php?option=com_resources&id=<?php echo $this->resource->id; ?>&active=usage&action=top&datetime=';
		
		function updateTables(yyyy, mm)
		{
			if (mm < 10) {
				mm = '0' + mm
			}

			var dt = yyyy + '/' + mm + '/01';
//console.log(dataurl + yyyy + '-' + mm);
			$.getJSON(dataurl + yyyy + '-' + mm + '&period=' + $('#period').val(), function(series){
				//console.log(series);
				if (!orgData[dt]) {
					orgData[dt] = series.orgs[dt];
				}
				if (!countryData[dt]) {
					countryData[dt] = series.countries[dt];
				}
				/*if (!domainData[dt]) {
					domainData[dt] = series.domains[dt];
				}*/
//console.log(dt);
				if (orgData[dt] && orgData[dt].length > 0) {
					populateTable('pie-org-data', orgData[dt]);
				}
				// Update countries pie chart
				if (countryData[dt] && countryData[dt].length > 0) {
					populateTable('pie-country-data', countryData[dt]);
				}
				// Update domains pie chart
				/*if (domainData[dt] && domainData[dt].length > 0) {
					populateTable('pie-domains-data', domainData[dt]);
				}*/
			});
		}
		
		function populateTable(id, data) 
		{
			var tbl = $('#' + id + ' tbody');

			tbl.empty();

			var footer = data.shift();
			var total = footer['data'];

			for (var i=0; i < data.length; i++)
			{
				tbl.append(
					'<tr>' +
						'<td class="textual-data">' + (data[i]['code'] ? '<img src="/core/components/com_members/site/assets/img/flags/' + data[i]['code'] + '.gif" alt="' + data[i]['code'] + '" /> ' : '') + data[i]['label'] + '</td>' + 
						'<td><span class="bar-wrap"><span class="bar" style="width: ' + Math.round(((data[i]['data']/total)*100),2) + '%;"></span><span class="value">' + data[i]['data'] + ' (' + Math.round(((data[i]['data']/total)*100),2) + '%)</span></span></td>' + 
						//'<td>' + Math.round(((data[i]['data']/total)*100),2) + '%</td>' + 
					'</tr>'
				);
			}
			data.unshift(footer);
		}
		
		var linesu = null;
		var linesr = null;
		
		function unmark()
		{
			if (linesr) linesr.destroy();
			if (linesu) linesu.destroy();
		}
		
		function mark(event, isSeriesClick) 
		{
			var chartr = window.runsChart,
				chartu = window.usersChart;

			if (isSeriesClick) {
				var xr = event.point.plotX + chartr.plotLeft,
					xu = event.point.plotX + chartu.plotLeft;
			} else {
				var xr = event.chartX,
					xu = event.chartX;
			}

			//if (linesr) linesr.destroy();
			//if (linesu) linesu.destroy();
			unmark();

			//Use above coordinates to draw line 
			linesr = chartr.renderer.path([
				'M',
				xr, chartr.plotTop,
				'L',
				xr, chartr.plotTop + chartr.plotHeight
			]).attr({
				'stroke-width': 1,
				stroke: 'red',
				id: 'vert',
				zIndex: 2000
			}).add();

			//Use above coordinates to draw line 
			linesu = chartu.renderer.path([
				'M',
				xu, chartu.plotTop,
				'L',
				xu, chartu.plotTop + chartu.plotHeight
			]).attr({
				'stroke-width': 1,
				stroke: 'red',
				id: 'vert',
				zIndex: 2000
			}).add();
		}

		$(function () {
			//var chart;
			var series = [{
				type : 'area',
				threshold : null,
				marker : {
					enabled : true,
					radius : 3
				},
				shadow : true,
				tooltip : {
					valueDecimals : 0
				},
				stickyTracking: false,
				fillColor: "<?php echo $this->params->get('chart_color_fill', 'rgba(0,0,0,0.15)'); ?>" 
			}];
			var settingsR = {
				chart: {
					animation: false,
					renderTo: '',
					borderRadius: 0
				},
				colors: [
					"<?php echo $this->params->get('chart_color_line', '#656565'); ?>"
				],
				rangeSelector : {
					selected : 1
				},
				scrollbar : {
					enabled : false
				},
				yAxis: {
					gridLineColor: '#eee',
					min: 0
				},
				tooltip: {
					formatter: function() {
						var point = this.points[0];
						return '<b>'+ point.series.name +'</b><br/>'+
								Highcharts.dateFormat('%B %Y', this.x) + ': '+
								Highcharts.numberFormat(point.y, 0);
					},
					shared: true
				},
				credits: {
					enabled: false
				},
				rangeSelector: {
					buttons: [{
						type: 'month',
						count: 3,
						text: '3m'
					}, {
						type: 'month',
						count: 6,
						text: '6m'
					}, /*{
						type: 'ytd',
						text: 'YTD'
					},*/ {
						type: 'year',
						count: 1,
						text: '1y'
					}, {
						type: 'all',
						text: 'All'
					}],
					selected: 3,
					inputEnabled: false
				},
				series : series,
				navigator: {
					series: {
						type : 'area',
						color: "<?php echo $this->params->get('chart_color_selection', '#656565'); ?>" 
					}
				}
			}
			var settingsU = {
				chart: {
					animation: false,
					renderTo: '',
					borderRadius: 0
				},
				colors: [
					"<?php echo $this->params->get('chart_color_line', '#656565'); ?>"
				],
				rangeSelector : {
					selected : 1
				},
				scrollbar : {
					enabled : false
				},
				yAxis: {
					gridLineColor: '#eee',
					min: 0
				},
				tooltip: {
					formatter: function() {
						var point = this.points[0];
						return '<b>'+ point.series.name +'</b><br/>'+
								Highcharts.dateFormat('%B %Y', this.x) + ': '+
								Highcharts.numberFormat(point.y, 0);
					},
					shared: true
				},
				credits: {
					enabled: false
				},
				rangeSelector: {
					buttons: [{
						type: 'month',
						count: 3,
						text: '3m'
					}, {
						type: 'month',
						count: 6,
						text: '6m'
					/*}, {
						type: 'ytd',
						text: 'YTD'*/
					}, {
						type: 'year',
						count: 1,
						text: '1y'
					}, {
						type: 'all',
						text: 'All'
					}],
					selected: 3,
					inputEnabled: false
				},
				/*title: {
					text: "<?php echo Lang::txt('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?>"
				},*/
				series : series,
				navigator: {
					series: {
						type : 'area',
						color: "<?php echo $this->params->get('chart_color_selection', '#656565'); ?>" 
					}
				}
			}
			
			$(document).ready(function() {
				//var settingsR = jQuery.extend(true, {}, settings);
				settingsR.chart.renderTo = 'runs-overview';
				settingsR.chart.events = {
					redraw: function(event) {
						var e = this.xAxis[0].getExtremes(),
							b = window.usersChart.xAxis[0].getExtremes();

						if (b.min != e.min || b.max != e.max) {
							window.usersChart.xAxis[0].setExtremes(e.min, e.max, true);
						}
					}
				};
				settingsR.yAxis.max = <?php echo $runsTop; ?>;
				settingsR.series[0].name ="<?php echo Lang::txt('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?>";
				settingsR.series[0].data = [<?php echo implode(',', $runs); ?>];
				settingsR.series[0].point = {
					events: {
						click: function() {
							d = new Date(this.x);
							//console.log(d.getFullYear() + '/' + d.getMonth() + '/' + d.getDate());
							//mark(event, true);
							//mark(event, true, 'users');
							updateTables(d.getFullYear(), (d.getMonth()+1));
							$('#runs-overview-total').text(this.y);
							$('#runs-overview-date').text(Highcharts.dateFormat('%b %Y', this.x));
							//console.log(window.usersChart.series[0].data);
							for (var i=0; i<window.usersChart.series[0].data.length; i++) 
							{
								//console.log(window.usersChart.series[0].data[i].x);
								if (window.usersChart.series[0].data[i] !== undefined && window.usersChart.series[0].data[i].x == this.x)
								{
									$('#users-overview-total').text(window.usersChart.series[0].data[i].y);
									$('#users-overview-date').text(Highcharts.dateFormat('%b %Y', window.usersChart.series[0].data[i].x));
									break;
								}
							}
						}
					}
				};

				window.runsChart = new Highcharts.StockChart(settingsR);
				//console.log(settingsR);
				//var settingsU = jQuery.extend(true, {}, settings);
				settingsU.chart.renderTo = 'users-overview';
				settingsU.chart.events = {
					redraw: function(event) {
						var e = this.xAxis[0].getExtremes(),
							b = window.runsChart.xAxis[0].getExtremes();

						if (b.min != e.min || b.max != e.max) {
							window.runsChart.xAxis[0].setExtremes(e.min, e.max, true);
						}
					}
				};
				settingsU.yAxis.max = <?php echo $usersTop; ?>;
				settingsU.series[0].name ="<?php echo Lang::txt('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?>";
				settingsU.series[0].data = [<?php echo implode(',', $users); ?>];
				settingsU.series[0].point = {
					events: {
						click: function() {
							d = new Date(this.x);
							//console.log(d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + d.getDate());
							//mark(event, true, 'runs');
							//mark(event, true, 'users');
							updateTables(d.getFullYear(), (d.getMonth()+1));
							$('#users-overview-total').text(this.y);
							$('#users-overview-date').text(Highcharts.dateFormat('%b %Y', this.x));
							
							for (var i=0; i<window.runsChart.series[0].data.length; i++) 
							{
								//console.log(window.usersChart.series[0].data[i].x);
								if (window.runsChart.series[0].data[i] !== undefined && window.runsChart.series[0].data[i].x == this.x)
								{
									$('#runs-overview-total').text(window.runsChart.series[0].data[i].y);
									$('#runs-overview-date').text(Highcharts.dateFormat('%b %Y', window.runsChart.series[0].data[i].x));
									break;
								}
							}
						}
					}
				};
				
				// Create the chart
				window.usersChart = new Highcharts.StockChart(settingsU);
				
				$('a.dataset').on('click', function(e){
					e.preventDefault();
					$('a.dataset').removeClass('active');
					$(this).addClass('active');
					$.getJSON($(this).attr('href'), function(data){
						
						var runs = [], users = [], runstop = 0, userstop = 0;

						for (var i=0; i<data.points.length; i++) 
						{
							date = Date.parse(data.points[i].datetime);

							users.push([
								date,
								parseInt(data.points[i].users)
							]);
							runs.push([
								date,
								parseInt(data.points[i].jobs)
							]);
							userstop = (parseInt(data.points[i].users) > userstop ? parseInt(data.points[i].users) : userstop);
							runstop  = (parseInt(data.points[i].jobs) > runstop   ? parseInt(data.points[i].jobs)  : runstop);
						}
						settingsR.yAxis[0].max = runstop;
						settingsR.series = series;
						/*settingsR.series = [{
							type : 'area',
							threshold : null,
							marker : {
								enabled : true,
								radius : 3
							},
							shadow : true,
							tooltip : {
								valueDecimals : 0
							},
							stickyTracking: false,
							fillColor : 'rgba(0,0,0,0.15)'
						}];*/
						settingsR.series[0].name ="<?php echo Lang::txt('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?>";
						settingsR.series[0].data = runs;
						//console.log(settingsR.series);
						window.runsChart = new Highcharts.StockChart(settingsR);
						
						settingsU.yAxis[0].max = userstop;
						settingsU.series = series;
						/*settingsU.series = [{
							type : 'area',
							threshold : null,
							marker : {
								enabled : true,
								radius : 3
							},
							shadow : true,
							tooltip : {
								valueDecimals : 0
							},
							stickyTracking: false,
							fillColor : 'rgba(0,0,0,0.15)'
						}];*/
						settingsU.series[0].name ="<?php echo Lang::txt('PLG_RESOURCES_USAGE_SIMULATION_USERS'); ?>";
						settingsU.series[0].data = users;
						//console.log(userstop);
						window.usersChart = new Highcharts.StockChart(settingsU);
					});
					return false;
				});
			});
		});
	}
		</script>
<br />
		<div class="two columns first">
			<table summary="<?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?>" id="pie-org-data" class="pie-chart">
				<caption><?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_2_CAPTION'); ?></caption>
				<thead>
					<tr>
						<!-- <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_NUM'); ?></th> -->
						<th scope="col"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_TYPE'); ?></th>
						<th scope="col" colspan="2" class="numerical-data"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php 

$colors = array(
	$this->params->get('pie_chart_color1', '#7c7c7c'),
	$this->params->get('pie_chart_color2', '#515151'),
	$this->params->get('pie_chart_color3', '#d9d9d9'),
	$this->params->get('pie_chart_color4', '#3d3d3d'),
	$this->params->get('pie_chart_color5', '#797979'),
	$this->params->get('pie_chart_color6', '#595959'),
	$this->params->get('pie_chart_color7', '#e5e5e5'),
	$this->params->get('pie_chart_color8', '#828282'),
	$this->params->get('pie_chart_color9', '#404040'),
	$this->params->get('pie_chart_color10', '#6a6a6a'),
	$this->params->get('pie_chart_color1', '#bcbcbc'),
	$this->params->get('pie_chart_color2', '#515151'),
	$this->params->get('pie_chart_color3', '#d9d9d9'),
	$this->params->get('pie_chart_color4', '#3d3d3d'),
	$this->params->get('pie_chart_color5', '#797979'),
	$this->params->get('pie_chart_color6', '#595959'),
	$this->params->get('pie_chart_color7', '#e5e5e5'),
	$this->params->get('pie_chart_color8', '#828282'),
	$this->params->get('pie_chart_color9', '#404040'),
	$this->params->get('pie_chart_color10', '#3a3a3a'),
);

//$datetime = date("Y") . '-' . (date("m") - 1);
$datetime = str_replace('-00 00:00:00', '', $result->datetime);

$tid = plgResourcesUsage::getTid($this->resource->id, $datetime, $prd);

if (intval($this->params->get('cache', 1)))
{
	if (!($dataset = Cache::get('resources.usage' . $this->resource->id . 'type')))
	{
		$dataset = plgResourcesUsage::getTopValue($this->resource->id, 3, $tid, $datetime, $prd);

		Cache::put('resources.usage' . $this->resource->id . 'type', $dataset, intval($this->params->get('cache_time', 15)));
	}
}
else 
{
	$dataset = plgResourcesUsage::getTopValue($this->resource->id, 3, $tid, $datetime, $prd);
}
$data = array();
$r = array();
//$results = null;
$total = 0;
$cls = 'even';
$tot = '';
$pieOrg = array();
//$toporgs = null;
if ($dataset)
{
	$i = 0;
	$data = array();
	$r = array();

	foreach ($dataset as $row)
	{
		$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
		if (!isset($data[$ky]))
		{
			$i = 0;
			$data[$ky] = array();
			$r[$ky] = array();
		}
		$data[$ky][] = $row;
		if (!isset($colors[$i]))
		{
			$i = 0;
		}
		$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.$row->value.', color: \''.$colors[$i].'\'}';

		if ($row->rank != '0') 
		{
			$total += $row->value;
		}

		$i++;
	}

	$i = 0;
	foreach ($dataset as $row)
	{
		if ($row->rank == '0') 
		{
			continue;
		}

		if ($row->name == '?') 
		{
			$row->name = Lang::txt('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

			$cls = ($cls == 'even') ? 'odd' : 'even';
?>
					<tr rel="<?php echo $row->name; ?>">
						<!-- <th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th> -->
						<td class="textual-data"><?php echo $row->name; ?></td>
						<td><span class="bar-wrap"><span class="bar" style="width: <?php echo round((($row->value/$total)*100),2); ?>%;"></span><span class="value"><?php echo number_format($row->value); ?> (<?php echo round((($row->value/$total)*100),2); ?>%)</span></span></td>
						<!-- <td><?php echo round((($row->value/$total)*100),2); ?>%</td> -->
					</tr>
<?php
						$i++;
					}
				}
				else 
				{
				?>
					<tr>
						<td colspan="3" class="textual-data"><?php echo Lang::txt('No data found for the month of %s', $datetime); ?></td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
			<script>
				var orgData = {
					<?php
					$z = array();
					foreach ($r as $k => $d)
					{
						$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
					}
					echo implode(',', $z);
					?>
				};
			</script>
		</div>
		<div class="two columns second">
			<table summary="<?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?>" id="pie-country-data" class="pie-chart">
				<caption><?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_3_CAPTION'); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_COUNTRY'); ?></th>
						<th scope="col" colspan="2" class="numerical-data"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if (intval($this->params->get('cache', 1)))
					{
						if (!($dataset = Cache::get('resources.usage' . $this->resource->id . 'country')))
						{
							$dataset = plgResourcesUsage::getTopValue($this->resource->id, 1, $tid, $datetime, $prd);

							Cache::put('resources.usage' . $this->resource->id . 'country', $dataset, intval($this->params->get('cache_time', 15)));
						}
					}
					else 
					{
						$dataset = plgResourcesUsage::getTopValue($this->resource->id, 1, $tid, $datetime, $prd);
					}

					$total = 0;
					$i = 0;
					if ($dataset)
					{
						$data = array();
						$r = array();
						$names = array();

						foreach ($dataset as $row)
						{
							$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
							if (!isset($data[$ky]))
							{
								$i = 0;
								$data[$ky] = array();
								$r[$ky] = array();
							}
							$data[$ky][] = $row;
							if (!isset($colors[$i]))
							{
								$i = 0;
							}

							$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.$row->value.', color: \''.$colors[$i].'\'}'."\n";

							if ($row->rank != '0') 
							{
								$total += $row->value;
							}
							
							$names[] = $row->name;

							$i++;
						}

						$codes = \Hubzero\Geocode\Geocode::getCodesByNames($names);

						$cls = 'even';
						//$pie = array();
						$i = 0;

						foreach ($dataset as $row)
						{
							if ($row->rank == '0') 
							{
								continue;
							}

							if ($row->name == '?') 
							{
								$row->name = Lang::txt('PLG_RESOURCES_USAGE_UNIDENTIFIED');
							}

							$cls = ($cls == 'even') ? 'odd' : 'even';
							?>
					<tr rel="<?php echo $row->name; ?>">
						<!-- <th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th> -->
						<td class="textual-data"><?php 
						if (isset($codes[$row->name])) { ?>
							<img src="/core/components/com_members/site/assets/img/flags/<?php echo strtolower($codes[$row->name]['code']); ?>.gif" alt="<?php echo strtolower($codes[$row->name]['code']); ?>" /> 
						<?php }
						echo $row->name; ?></td>
						<td><span class="bar-wrap"><span class="bar" style="width: <?php echo round((($row->value/$total)*100),2); ?>%;"></span><span class="value"><?php echo number_format($row->value); ?> (<?php echo round((($row->value/$total)*100),2); ?>%)</span></span></td>
						<!-- <td><?php echo round((($row->value/$total)*100),2); ?>%</td> -->
					</tr>
							<?php
							$i++;
						}
					}
					else 
					{
					?>
					<tr>
						<td colspan="3" class="textual-data"><?php echo Lang::txt('No data found for the month of %s', $datetime); ?></td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<script>
				var countryData = {
					<?php
					$z = array();
					foreach ($r as $k => $d)
					{
						$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
					}
					echo implode(',', $z);
					?>
				};
			</script>
		</div>
<?php /*
			<table summary="<?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?>" id="pie-domains-data" class="pie-chart">
				<caption><?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_4_CAPTION'); ?></caption>
				<thead>
					<tr>
						<!-- <th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_NUM'); ?></th> -->
						<th scope="col"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_DOMAINS'); ?></th>
						<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_USERS'); ?></th>
						<th scope="col" class="numerical-data"><?php echo Lang::txt('PLG_RESOURCES_USAGE_COL_PERCENT'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php 
if (intval($this->params->get('cache', 1)))
{
	if (!($results = Cache::get('resources.usage' . $this->resource->id . 'domains')))
	{
		$results = plgResourcesUsage::getTopValue($this->resource->id, 2, $tid, $datetime);

		Cache::put('resources.usage' . $this->resource->id . 'domains', $results, intval($this->params->get('cache_time', 15)));
	}
}
else 
{
	$results = plgResourcesUsage::getTopValue($this->resource->id, 2, $tid, $datetime);
}

$total = 0;
$i = 0;
if ($results)
{
	$data = array();
	$r = array();
	foreach ($results as $row)
	{
		$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
		if (!isset($data[$ky]))
		{
			$i = 0;
			$data[$ky] = array();
			$r[$ky] = array();
		}
		$data[$ky][] = $row;
		if (!isset($colors[$i]))
		{
			$i = 0;
		}
		$r[$ky][] = '{label: \''.addslashes($row->name).'\', data: '.number_format($row->value).', color: \''.$colors[$i].'\'}';

		if ($row->rank != '0') 
		{
			$total += $row->value;
		}

		$i++;
	}

	$cls = 'even';
	$tot = '';

	$i = 0;
	foreach ($results as $row)
	{
		if ($row->rank == '0') 
		{
			continue;
		}

		if ($row->name == '?') 
		{
			$row->name = Lang::txt('PLG_RESOURCES_USAGE_UNIDENTIFIED');
		}

			$cls = ($cls == 'even') ? 'odd' : 'even';
			?>
					<tr rel="<?php echo $row->name; ?>">
						<!-- <th><span style="background-color: <?php echo $colors[$i]; ?>"><?php echo $row->rank; ?></span></th> -->
						<td class="textual-data"><?php echo $row->name; ?></td>
						<td><?php echo number_format($row->value); ?></td>
						<td><?php echo round((($row->value/$total)*100),2); ?></td>
					</tr>
			<?php
			$i++;
	}
	echo $tot;
}
else 
{
				?>
					<tr>
						<td colspan="3" class="textual-data"><?php echo Lang::txt('No data found for the month of %s', $datetime); ?></td>
					</tr>
				<?php
}
?>
				</tbody>
			</table>
			<script>
				var domainData = {
					<?php
					$z = array();
					foreach ($r as $k => $d)
					{
						$z[] = "\t'$k': [" . implode(',', $d) . "]" . "\n";
					}
					echo implode(',', $z);
					?>
				};
			</script> */ ?>
			</div><!-- / .four columns second third fourth -->
			<div style="clear:left;"></div>
		</div><!-- / #user-overview-wrap -->
		
		<div id="runs-overview-wrap" class="usage-wrap">
			<div class="four columns first">
				<h4><?php echo Lang::txt('PLG_RESOURCES_USAGE_SIMULATION_RUNS'); ?></h4>
				<p class="total">
					<strong id="runs-overview-total"><?php echo number_format($current->jobs); ?></strong>
					<span id="runs-overview-date"><time datetime="<?php echo $result->datetime; ?>"><?php echo JHTML::_('date', $result->datetime, $dateFormat, $tz); ?></time></span></span>
				</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third fourth">
				<div id="runs-overview" style="min-width:400px;height:250px;">
				<?php
				if ($results)
				{
					// Find the highest value
					$vals = array();
					foreach ($results as $result)
					{
						$vals[] = $result->jobs;
					}
					asort($vals);

					$highest = array_pop($vals);

					$sparkline  = '<span class="sparkline">' . "\n";
					foreach ($results as $result)
					{
						$height = round(($result->jobs / $highest)*100);
						$sparkline .= "\t" . '<span class="index">';
						$sparkline .= '<span class="count" style="height: ' . $height . '%;" title="' . JHTML::_('date', $result->datetime, $dateFormat, $tz) . ': ' . $result->jobs . '">';
						$sparkline .= $result->jobs; //trim($this->_fmt_result($result->value, $result->valfmt));
						$sparkline .= '</span> ';
						$sparkline .= '</span>' . "\n";
					}
					$sparkline .= '</span>' . "\n";
					echo $sparkline;
				} 
				?>
				</div>
				
				<table summary="<?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_1_CAPTION'); ?>" id="pie-runs-data" class="pie-chart">
					<caption><?php echo Lang::txt('PLG_RESOURCES_USAGE_TBL_1_CAPTION'); ?></caption>
					<thead>
						<tr>
							<th scope="col" class="numerical-data"></th>
							<th scope="col" class="numerical-data"><?php echo Lang::txt('Average'); ?></th>
							<th scope="col" class="numerical-data"><?php echo Lang::txt('Total'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>
								<?php echo Lang::txt('PLG_RESOURCES_USAGE_WALL_TIME'); ?>
							</th>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->avg_wall); ?>
							</td>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->tot_wall); ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo Lang::txt('PLG_RESOURCES_USAGE_CPU_TIME'); ?>
							</th>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->avg_cpu); ?>
							</td>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->tot_cpu); ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo Lang::txt('PLG_RESOURCES_USAGE_INTERACTION_TIME'); ?>
							</th>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->avg_view); ?>
							</td>
							<td>
								<?php echo plgResourcesUsage::timeUnits($current->tot_view); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div><!-- / .four columns second third fourth -->
			<div style="clear:left;"></div>
		</div><!-- / #runs-overview-wrap -->
	</form>
