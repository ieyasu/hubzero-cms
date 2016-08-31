var taxonomy = {
	dataset: [],
	chart: 'barz',
	extension:'vert',
	datasource: [],
	
	xaxis: [],
	
	yaxis: [],
	
	levels: [],
	bgLevels: false,
	options: {
		xaxis:{
			min: 0,	 // => part of the series is not displayed.
			max: 60,	// => part of the series is not displayed.
			labelsAngle: 0,
			positionTop: true
		},
		yaxis:{
			min: 0,
			max: 90
		},
		grid: {
			outlineWidth: 2,       // => width of the grid outline/border in pixels
			circular: false        // => if set to true, the grid will be circular, must be used when radars are drawn
		},
		shadowSize: 0,
		mouse: {
			track: true,          // => true to track the mouse, no tracking otherwise
			position: 'se',        // => position of the value box (default south-east)
			relative: true,       // => next to the mouse cursor
			//trackFormatter: Flotr.defaultTrackFormatter, // => formats the values in the value box
			trackFormatter: function(obj){ 
				//var myAjax = new Ajax('http://nanohub.org/index.php?option=com_resources&task=browser&no_html=1&type=7&level=1', {method: 'get'}).request();
				//response = '';
				/*new Ajax.Updater('flotr-mouse-value', 'http://nanohub.org/index.php?option=com_resources&task=browser&no_html=1&type=7&level=1', { 
					method:'get'
				});*/
				//return response;
				url = obj.series.data[obj.index][5];
				
				if (url.indexOf('/resources/') !=-1 || url.indexOf('/tools/') !=-1) {
					bits = url.split('/');
					id = bits.pop();
					if ($('r-'+id)) {
						var html = $('r-'+id).innerHTML;
					} else {
						var html = obj.series.data[obj.index][3]; // +'<br />' + obj.series.data[obj.index][5];
					}
				} else {
					var html = obj.series.data[obj.index][3];
				}
				return html;
			},
			margin: 5,             // => margin in pixels of the valuebox
			lineColor: '#FF3F19',  // => line color of points that are drawn when mouse comes near a value of a series
			trackDecimals: 1,      // => decimals for the track values
			sensibility: 2,        // => the lower this number, the more precise you have to aim to show a value
			radius: 3,             // => radius of the track point
			fillColor: null,       // => color to fill our select bar with only applies to bar and similar graphs (only bars for now)
			fillOpacity: 0.4       // => opacity of the fill color, set to 1 for a solid fill, 0 hides the fill 
		}
	},
	
	checkXAxisOptions: function() {
		var ticks = [];
		var max = taxonomy._options.xaxis.max;
		for (var i = 0; i < taxonomy.xaxis.length; ++i) {
			if ($('show'+taxonomy.xaxis[i][3])) {
				if ($('show'+taxonomy.xaxis[i][3]).checked) {
					taxonomy.xaxis[i][2] = true;
					ticks.push(taxonomy._options.xaxis.ticks[i+1]);
					ticks.push(taxonomy._options.x2axis.ticks[i+1]);
				} else {
					taxonomy.xaxis[i][2] = false;
					max -= 10;
				}
			} else {
				taxonomy.xaxis[i][2] = true;
				ticks.push(taxonomy._options.xaxis.ticks[i+1]);
				ticks.push(taxonomy._options.x2axis.ticks[i+1]);
			}
		}
		//taxonomy.options.xaxis.max = max;
		//taxonomy.options.x2axis.max = max;
		//taxonomy.options.xaxis.ticks = ticks;
	},
	
	checkYAxisOptions: function() {
		var ticks = [];
		var max = taxonomy._options.yaxis.max;
		for (var i = 0; i < taxonomy.yaxis.length; ++i) {
			if ($('show'+taxonomy.yaxis[i][3])) {
				if ($('show'+taxonomy.yaxis[i][3]).checked) {
					taxonomy.yaxis[i][2] = true;
					ticks.push(taxonomy._options.yaxis.ticks[i+1]);
				} else {
					taxonomy.yaxis[i][2] = false;
					max -= 10;
				}
			} else {
				taxonomy.yaxis[i][2] = true;
				ticks.push(taxonomy._options.yaxis.ticks[i+1]);
			}
		}
		//taxonomy.options.yaxis.max = max;
		//taxonomy.options.yaxis.ticks = ticks;
	},
	
	checkLevelOptions: function() {
		for (var i = 0; i < taxonomy.levels.length; ++i) {
			if ($('show'+taxonomy.levels[i][3])) {
				if ($('show'+taxonomy.levels[i][3]).checked) {
					taxonomy.levels[i][2] = true;
				} else {
					taxonomy.levels[i][2] = false;
				}
			} else {
				taxonomy.levels[i][2] = true;
			}
		}
	},
	
	rebuildPlot: function(d) {
		var dataset = [];
		var d4 = [];
		var d5 = [];

		taxonomy.checkXAxisOptions();
		for (var i = 0; i < d.length; ++i) 
		{
			include = true;
			for (var z = 0; z < taxonomy.xaxis.length; ++z) 
			{
				if (taxonomy.xaxis[z][2] == false) {
					x = d[i][0];
					if (taxonomy.extension == 'hori' && d[i][2] > 10) {
						x = d[i][0] - (d[i][2]/2);
					}
					if (x >= taxonomy.xaxis[z][0] && x < taxonomy.xaxis[z][1]) {
						include = false;
					}
				}
			}
			if (include) {
				d4.push(d[i]);
			}
		}

		taxonomy.checkYAxisOptions();
		for (var i = 0; i < d4.length; ++i) 
		{
			include = true;
			for (var z = 0; z < taxonomy.yaxis.length; ++z) 
			{
				if (taxonomy.yaxis[z][2] == false) {
					y = d4[i][1];
					if (taxonomy.extension == 'vert' && d4[i][2] > 10) {
						y = d4[i][1] - (d4[i][2]/2);
					}
					if (y >= taxonomy.yaxis[z][0] && y < taxonomy.yaxis[z][1]) {
						include = false;
					}
				}
			}
			if (include) {
				d5.push(d4[i]);
			}
		}
		
		var d6 = [];
		taxonomy.checkLevelOptions();
		for (var i = 0; i < d5.length; ++i) 
		{
			include = true;
			for (var z = 0; z < taxonomy.levels.length; ++z) 
			{
				if (taxonomy.levels[z][2] == false) {
					if (d5[i][6]) {
						if (d5[i][6] >= taxonomy.levels[z][0] && d5[i][6] < taxonomy.levels[z][1]) {
							include = false;
						}
					}
				}
			}
			if (include) {
				d6.push(d5[i]);
			}
		}
		
		// Check for NCN Supported status
		if ($('showsupported')) {
			if ($('showsupported').checked) {
				supported = true;
			} else {
				supported = false;
			}
			for (var i = 0; i < d6.length; ++i) 
			{
				if (supported || (!supported && !d6[i][9])) {
					dataset.push(d6[i]);
				}
			}
		} else {
			dataset = d6;
		}
		
		//alert(taxonomy.yaxis);
		if ($('displaylabels') && !$('displaylabels').checked) {
			showLabels = false;
		} else {
			showLabels = true;
		}

		if (taxonomy.chart == 'barz') {
			f = Flotr.draw($('container'), [{data:dataset, barz:{show:true,showLabels: showLabels,bgLevels:taxonomy.bgLevels}, dotz:{show:false}}], taxonomy.options);
		} else {
			f = Flotr.draw($('container'), [{data:dataset, dotz:{show:true,showLabels: showLabels,bgLevels:taxonomy.bgLevels,extension:taxonomy.extension}, barz:{show:false}}], taxonomy.options);
		}
	},
	
	setup: function() {
		taxonomy._xaxis = taxonomy.xaxis;
		taxonomy._yaxis = taxonomy.yaxis;
		taxonomy._options = taxonomy.options;
		taxonomy._levels = taxonomy.levels;
		
		// Click event - redirects to a URL
		$('container').observe('flotr:clickHit', function(event){
			var position = event.memo[0];
			window.location = position.series.data[position.index][5];
		});
		
		// Create X-Axis options
		if ($('xaxis')) {
			for (var i = 0; i < taxonomy.xaxis.length; ++i) {
				var lbl = document.createElement('label');
				
				var spn = document.createElement('span');
				spn.innerHTML = ' '+taxonomy.options.xaxis.ticks[i+1][1];
				
				var inpt = document.createElement('input');
				inpt.type = 'checkbox';
				inpt.name = 'show'+taxonomy.xaxis[i][3];
				inpt.id = 'show'+taxonomy.xaxis[i][3];
				inpt.className = 'option';
				inpt.checked = true;
				
				lbl.appendChild(inpt);
				lbl.appendChild(spn);
				
				$('xaxis').appendChild(lbl);
			}
		}
		// Create Y-Axis options
		if ($('yaxis')) {
			taxonomy.yaxis.reverse();
			taxonomy.options.yaxis.ticks.reverse();
			for (var i = 0; i < taxonomy.yaxis.length; ++i) {
				var lbl = document.createElement('label');
				
				var spn = document.createElement('span');
				spn.innerHTML = ' '+taxonomy.options.yaxis.ticks[i][1];
				
				var inpt = document.createElement('input');
				inpt.type = 'checkbox';
				inpt.name = 'show'+taxonomy.yaxis[i][3];
				inpt.id = 'show'+taxonomy.yaxis[i][3];
				inpt.className = 'option';
				inpt.checked = true;
				
				lbl.appendChild(inpt);
				lbl.appendChild(spn);
				
				$('yaxis').appendChild(lbl);
			}
			taxonomy.yaxis.reverse();
			taxonomy.options.yaxis.ticks.reverse();
		}

		// X-Axis Options
		for (var i = 0; i < taxonomy.xaxis.length; ++i) {
			if ($('show'+taxonomy.xaxis[i][3])) {
				$('show'+taxonomy.xaxis[i][3]).observe('change', function(event){
					taxonomy.rebuildPlot(taxonomy.datasource);
				});
			}
		}

		// Y-Axis options
		for (var i = 0; i < taxonomy.yaxis.length; ++i) {
			if ($('show'+taxonomy.yaxis[i][3])) {
				$('show'+taxonomy.yaxis[i][3]).observe('change', function(event){
					taxonomy.rebuildPlot(taxonomy.datasource);
				});
			}
		}

		// NCN Supported
		if ($('showsupported')) {
			$('showsupported').observe('change', function(event){
				taxonomy.rebuildPlot(taxonomy.datasource);
			});
		}
		
		// Audience levels
		for (var i = 0; i < taxonomy.levels.length; ++i) {
			if ($('show'+taxonomy.levels[i][3])) {
				$('show'+taxonomy.levels[i][3]).observe('change', function(event){
					taxonomy.rebuildPlot(taxonomy.datasource);
				});
			}
		}

		// Text labels on plotted items
		if ($('displaylabels')) {
			$('displaylabels').observe('change', function(event){
				taxonomy.rebuildPlot(taxonomy.datasource);
			});
		}
	}
};