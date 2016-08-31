<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
<div class="main section" id="taxonomy-section">
	<div class="info">
		<h4>Help Us Out!</h4>
		<p>We at nanoHUB.org are committed to improving the user experience and are experimenting with new, different ways to represent data and browse content. We want to make finding the content you're interested in easier and quicker. Please leave <a href="/feedback">feedback</a> when you have a few moments!</p>
	</div>
	<div id="taxonomy">
<?php if ($this->type == 7) { ?>
		<p class="warning"><strong>Note:</strong> Only nanoelectronic tools are currently represented.</p>
<?php } ?>
		<div class="aside">
			<form id="taxonomy-controls" action="">
				<fieldset id="xaxis">
					<legend>Show <?php if ($this->type == 6) { ?>Audience<?php } else { ?>Concepts<?php } ?>:</legend>
				</fieldset>
				<fieldset id="yaxis">
					<legend>Show <?php if ($this->type == 6) { ?>Topics<?php } else { ?>Devices<?php } ?>:</legend>
				</fieldset>
<?php if ($this->type == 6) { ?>
				<p class="info">The size of the dot corresponds to the size (number of lectures) of the course.</p>
<?php } else if ($this->type == 7) { ?>
				<fieldset id="extras">
					<legend>Extras:</legend>
					<label><input class="option" type="checkbox" name="showsupported" id="showsupported" value="1" checked="checked" /> NCN Supported</label>
				</fieldset>
<?php } ?>
			</form>
		</div><!-- / .aside -->
		<div class="subject">
			<div id="container" class="graph" style="width:700px;height:800px;"></div>
<?php if ($this->type == 7) { ?>
			<div id="filteroptions">
				<div style="margin-left: 120px;">
					Show:
					<!-- <label class="skill_level0"><input class="option" type="checkbox" name="showfreshman" id="showfreshman" value="1" checked="checked" />Freshman</label> -->
					<label class="skill_level1"><input class="option" type="checkbox" name="showsophmore" id="showsophmore" value="1" checked="checked" />Easy</label>
					<!-- <label class="skill_level2"><input class="option" type="checkbox" name="showjunior" id="showjunior" value="1" checked="checked" />Junior</label> -->
					<label class="skill_level2"><input class="option" type="checkbox" name="showsenior" id="showsenior" value="1" checked="checked" />Intermediate</label>
					<label class="skill_level3"><input class="option" type="checkbox" name="showms" id="showms" value="1" checked="checked" />Advanced</label>
					<label class="skill_level4"><input class="option" type="checkbox" name="showphd" id="showphd" value="1" checked="checked" />Expert</label>
				</div>
			</div><!-- / #filteroptions -->
<?php } ?>
		</div><!-- / .subject -->
	</div>
	
	<script type="text/javascript" src="<?php echo (App::get('template')->protected ? '/core' : '/app'); ?>/templates/<?php echo App::get('template')->template; ?>/html/<?php echo $this->option; ?>/browse/js/prototype-1.6.0.3.js"></script>
	<!--[if IE]>
		<script type="text/javascript" src="<?php echo (App::get('template')->protected ? '/core' : '/app'); ?>/templates/<?php echo App::get('template')->template; ?>/html/<?php echo $this->option; ?>/browse/js/excanvas.js"></script>
		<script type="text/javascript" src="<?php echo (App::get('template')->protected ? '/core' : '/app'); ?>/templates/<?php echo App::get('template')->template; ?>/html/<?php echo $this->option; ?>/browse/js/base64.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo (App::get('template')->protected ? '/core' : '/app'); ?>/templates/<?php echo App::get('template')->template; ?>/html/<?php echo $this->option; ?>/browse/js/canvas2image.js"></script>
	<script type="text/javascript" src="<?php echo (App::get('template')->protected ? '/core' : '/app'); ?>/templates/<?php echo App::get('template')->template; ?>/html/<?php echo $this->option; ?>/browse/js/canvastext.js"></script>
	<script type="text/javascript" src="<?php echo (App::get('template')->protected ? '/core' : '/app'); ?>/templates/<?php echo App::get('template')->template; ?>/html/<?php echo $this->option; ?>/browse/js/flotr.js"></script>
	<script type="text/javascript" src="<?php echo (App::get('template')->protected ? '/core' : '/app'); ?>/templates/<?php echo App::get('template')->template; ?>/html/<?php echo $this->option; ?>/browse/js/taxonomy.js"></script>
<?php if ($this->type == 6) { ?>
	<script type="text/javascript">
	/**
	 * Wait till dom's finished loading.
	 */
	document.observe('dom:loaded', function(){

		taxonomy.xaxis = [
			[0,  10, false, "freshman", "#39b54a"],
			[10, 20, false, "sophomore", "#39b54a"],
			[20, 30, false, "junior", "#00aeef"], //#1b75bc
			[30, 40, false, "senior", "#00aeef"], //#1b75bc
			[40, 50, false, "ms", "#0d3f89"], //#1b75bc
			[50, 60, false, "phd", "#231f20"]
		];
		taxonomy.yaxis = [
			[0,  10, false, "computation"],
			[10, 20, false, "chemistry"],
			[20, 30, false, "bio"],
			[30, 40, false, "mechanics"],
			[40, 50, false, "photonics"],
			[50, 60, false, "materials"],
			[60, 70, false, "theory"],
			[70, 80, false, "nanotransistors"],
			[80, 90, false, "fundamentals"]
		];
		
		//taxonomy.options.xaxis.title = 'Audience';
		taxonomy.options.xaxis.ticks = [
			[0, ""], 
			[10, "Freshman"], 
			[20, "Sophomore"], 
			[30, "Junior"], 
			[40, "Senior"], 
			[50, "MS"], 
			[60, "PhD"]
		];
		taxonomy.options.xaxis.showLabels = false;
		taxonomy.options.fontSize = 9;
		taxonomy.options.HtmlText = false;
		taxonomy.options.x2axis = {
			color:'#000000', 
			max: 60, 
			title: 'Audience',
			showLabels: true,
			ticks: [
				[0, ""], 
				[10, "Freshman"], 
				[20, "Sophomore"], 
				[30, "Junior"], 
				[40, "Senior"], 
				[50, "MS"], 
				[60, "PhD"]
			]
		};
		
		taxonomy.options.yaxis.title = 'Topics';
		//taxonomy.options.yaxis.titleAngle = 90;
		taxonomy.options.yaxis.ticks = [
			[0, ""], 
			[10, "Computation"], 
			[20, "Chemistry"], 
			[30, "Bio"], 
			[40, "Mechanics"], 
			[50, "Photonics"], 
			[60, "Materials"], 
			[70, "Electronics\nTheory"], 
			[80, "Electronics\nNanotransistors"], 
			[90, "Electronics\nFundamentals"]
		];
		
		taxonomy.levels = taxonomy.xaxis;
		
		//[x, y, span, title, active, url, audience level start, audience level end, magnitude, NCN supported]
		taxonomy.datasource = [
			//[0, 0, 0, "", 0, ""], // 1
			//[0, 0, 0, "", 0, ""], // 2
			[50, 35, 20, "BioMEMs", 0, "/resources/180", 40, 60, 4], // 3
			//[0, 0, 0, "", 0, ""], // 4
			//[0, 0, 0, "", 0, ""], // 5
			//[0, 0, 0, "", 0, ""], // 6
			//[0, 0, 0, "", 0, ""], // 7
			[50, 25, 20, "Intro Bio-Med", 0, "/resources/3046", 40, 60, 21], // 8
			//[45, 25, 10, "Carrior Transport at the Nanoscale", 0, "/resources/3589"], // 9
			[50, 66, 20, "Graphene", 0, "/resources/7180", 40, 60, 6], // 10
			[50, 5, 20, "Computational Electronics", 0, "/resources/1500", 40, 60, 15], // 11
			[50, 81.5, 20, "Computational Electronics", 0, "/resources/1500", 40, 60, 15], // 11
			[50, 60.75, 20, "Quantum Transport", 0, "/resources/2039", 40, 60, 9], // 12
			//[45, 60.75, 11, "Curriculum on Nanotechnology", 0, "/resources/100"], // 13
			[35, 65, 10, "Nanoelectronics", 0, "/resources/5346", 30, 30, 41], // 14
			[50, 88, 20, "Semiconductor Fundamentals", 0, "/resources/5749", 40, 60, 40], // 15
			//[50, 75, 10, "Nanotransistors (Fall 2006)", 0, "/resources/1705"], // 16
			[55, 76, 10, "Nanotransistors (Fall 2008)", 0, "/resources/5328", 50, 50, 27], // 17 
			[50, 84, 20, "Advanced Semiconductors", 0, "/resources/7281", 40, 60, 36], // 18
			[55, 65, 10, "Advanced Nanoelectronics", 0, "/resources/6172", 50, 50, 42], // 19
			[55, 42, 10, "Nanophotonics", 0, "/resources/1748", 50, 50, 15], // 20
			[30, 62, 20, "Introduction to Nanotechnology", 0, "/resources/6583", 20, 40, 9], // 21
			[30, 52.5, 20, "Introduction to Nanotechnology", 0, "/resources/6583", 20, 40, 9], // 21
			[30, 42, 20, "Introduction to Nanotechnology", 0, "/resources/6583", 20, 40, 9], // 21
			[30, 32, 20, "Introduction to Nanotechnology", 0, "/resources/6583", 20, 40, 9], // 21
			[30, 22, 20, "Introduction to Nanotechnology", 0, "/resources/6583", 20, 40, 9], // 21
			//[50, 40, 10, "Fundamentals of Nanoelectronics (Fall 2004)", 0, "/resources/626"], // 22
			//[50, 40, 10, "Illinois CEE 595: Structural Engineering Seminar Series Lecture 1", 0, "/resources/6868"], // 23
			[35, 83, 10, "Semiconductor", 0, "/resources/5221", 30, 30, 20], // 24
			[35, 45, 10, "Optical Imaging", 0, "/resources/5163", 30, 30, 6], // 25
			[35, 5, 10, "Parallel Programming/GPU", 0, "/resources/7225", 30, 30, 15], // 26
			[45, 83, 10, "Hot Chips", 0, "/resources/6163", 40, 40, 7], // 27
			[45, 32, 10, "Hot Chips", 0, "/resources/6163", 40, 40, 7], // 27
			[15, 55, 10, "Intro materials", 0, "/resources/5220", 10, 10, 12], // 28
			//[10, 55, 10, "Atomic-Scale Simulation", 0, "/resources/6164"], // 29
			[35, 58, 10, "Intro Nanotech", 0, "/resources/7313", 30, 30, 25], // 30
			//[30, 57, 10, "Introduction to Biological Physics", 0, "/resources/4255"], // 31
			//[30, 57, 10, "Lecture on Molecular Dynamics of Materials", 0, "/resources/3675"], // 32
			//[0, 0, 0, "", 0, ""], // 33
			[35, 38, 10, "Atomic Force Microscopy", 0, "/resources/7320", 30, 30, 16], // 34
			[50, 44, 20, "Metamaterials", 0, "/resources/4262", 40, 60, 3], // 35
			[25, 54, 10, "Nanomaterials", 0, "/resources/1914", 20, 20, 19], // 36
			[40, 51, 20, "Electron Microscopy", 0, "/resources/3777", 30, 50, 12], // 37
			[40, 54, 20, "Intro to Molecular Dynamics", 0, "/resources/5838", 30, 50, 9], // 38
			[55, 52, 10, "Electron Microscopy", 0, "/resources/4092", 50, 50, 16], // 39
			[50, 64.25, 20, "Resistance", 0, "/resources/7168", 40, 60, 14], // 40
			[50, 67.75, 20, "Percolation & Reliability", 0, "/resources/", 40, 60, 6], // 41
			[30, 56, 20, "Computational Nanaoscience", 0, "/resources/3944", 20, 40, 33], // 42
			//[45, 73, 10, "Percolation Theory", 0, "/resources/5660"], // 43
			[50, 73, 20, "nanoMOSFETs", 0, "/resources/5306", 40, 60, 10], // 44
			[50, 77, 20, "Reliability Physics of Nanoscale Transistors", 0, "/resources/16560", 40, 40, 10],
			[40, 15, 20, "CHM 696: Supramolecular and Nanostructured Materials", 0, "/resources/10888", 20, 20, 10], // 11
			//[45, 73, 10, "Quantom Transport: Atom to Transistor (2004)", 0, "/resources/1490"], // 45
			//[45, 73, 10, "Reliability Physics of Nanoscale Transistors", 0, "/resources/16560"], // 46
			[50, 58, 20, "Molecular Dynamics Short Course", 0, "/resources/7570", 40, 60, 10], // 47
			//[0, 0, 0, "", 0, ""], // 48
			//[0, 0, 0, "", 0, ""], // 49
			//[0, 0, 0, "", 0, ""], // 50
			[50, 62.5, 20, "Atomistic Nanoelectronics", 0, "/resources/8086", 40, 60, 22], // 51
			//[0, 0, 0, "", 0, ""], // 52
			[40, 86, 40, "Semiconductor Fundamentals", 0, "/tools/abacus", 20, 50, 12], // 53
			[40, 69.5, 20, "Quantum Mechanics for Engineers", 0, "/topics/antsy", 30, 50, 10], // 54
			[20, 58.75, 20, "Nanotech Survey Course", 0, "/topics/antsy", 10, 20, 10] // 55
			//[0, 0, 0, "", 0, ""], // 56
			//[5, 15, 10, "Intro to Chemistry", 0, "/resources/", 0, 0, 4] // 57
		];
		taxonomy.bgLevels = true;
		taxonomy.chart = 'dotz';
		taxonomy.extension = 'hori';
		
		f = Flotr.draw($('container'), [{data:taxonomy.datasource, dotz:{show:true,bgLevels:taxonomy.bgLevels,extension:taxonomy.extension}, barz:{show:false}}], taxonomy.options);
		taxonomy.setup();
	});
	</script>
	<?php
	$ids = array(180,2046,7180,1500,2039,5346,5749,5328,7281,6172,1748,6583,5221,5163,7225,6163,5220,7313,7320,4262,1914,3777,5838,4092,7168,3944,5306,7570,8086,16560,10888);
	$sql = "SELECT C.id, C.title, C.type, C.introtext, C.fulltxt, C.created, C.created_by, C.modified, C.published, C.publish_up, C.standalone, C.hits, C.rating, C.times_rated, C.params, C.alias, C.ranking, t.type AS typetitle, lt.type AS logicaltitle 
		FROM #__resources AS C 
		LEFT JOIN #__resource_types AS t ON C.type=t.id 
		LEFT JOIN #__resource_types AS lt ON C.logical_type=lt.id 
		WHERE C.published=1 AND C.standalone=1 AND C.id IN ('".implode("','",$ids)."')";
	?>
<?php } else if ($this->type == 7) { ?>
	<script type="text/javascript">
	/**
	 * Wait till dom's finished loading.
	 */
	document.observe('dom:loaded', function(){

		taxonomy.xaxis = [
			[0,  10, false, "manbody", "#39b54a"],
			[10, 20, false, "atomistic", "#39b54a"],
			[20, 30, false, "spin", "#1b75bc"],
			[30, 40, false, "quantum", "#1b75bc"],
			[40, 50, false, "montecarlo", "#0d3f89"],
			[50, 60, false, "driftdiffusion", "#231f20"],
			[60, 70, false, "fundamentals", "#231f20"],
			[70, 80, false, "reliability", "#231f20"],
			[80, 90, false, "thermal", "#231f20"],
			[90, 100, false, "processing", "#231f20"],
			[100, 110, false, "circuits", "#231f20"]
		];
		taxonomy.yaxis = [
			[0, 10, false, "nanomagnet"],
			[10, 20, false, "memristor"],
			[20, 30, false, "solarcell"],
			[30, 40, false, "pnjunctions"],
			[40, 50, false, "moscap"],
			[50, 60, false, "mosfet"],
			[60, 70, false, "bjt"],
			[70, 80, false, "mesfet"],
			[80, 90, false, "finfet"],
			[90, 100, false, "hfet"],
			[100, 110, false, "nanofet"],
			[110, 120, false, "nanowire"],
			[120, 130, false, "cnt"],
			[130, 140, false, "gnr"],
			[140, 150, false, "superlattice"],
			[150, 160, false, "heterostructures"],
			[160, 170, false, "rtd"],
			[170, 180, false, "quantumdot"],
			[180, 190, false, "molecules"],
			[190, 200, false, "triangularcoulombwell"],
			[200, 210, false, "squarewell"],
			[210, 220, false, "harmonicpotential"],
			[220, 230, false, "fullerene"],
			[230, 240, false, "bulk"],
			[240, 250, false, "crystals"]
		];
		// Concepts
		taxonomy.options.xaxis.ticks = [
			[0, ""], 
			[10, "Many-Body"], 
			[20, "Atomistic"], 
			[30, "Spin"], 
			[40, "Quantum"], 
			[50, "Monte-Carlo"], 
			[60, "Drift Diffusion"],
			[70, "Fundamentals"],
			[80, "Reliability/Percolation"],
			[90, "Thermal/Phonons"],
			[100, "Processing"],
			[110, "Circuit"]
		];
		taxonomy.options.fontSize = 9;
		taxonomy.options.HtmlText = false;
		taxonomy.options.xaxis.max = 110;
		taxonomy.options.xaxis.showLabels = false;
		taxonomy.options.x2axis = {
			color:'#000000', 
			max: 110, 
			title: 'Concepts',
			showLabels: true,
			labelsAngle: 90,
			ticks: [
				[0, ""], 
				[10, "Many-Body"], 
				[20, "Atomistic"], 
				[30, "Spin"], 
				[40, "Quantum"], 
				[50, "Monte-Carlo"], 
				[60, "Drift Diffusion"],
				[70, "Fundamentals"],
				[80, "Reliability/\nPercolation"],
				[90, "Thermal/\nPhonons"],
				[100, "Processing"],
				[110, "Circuit"]
			]
		};
		// Devices
		taxonomy.options.yaxis.title = 'Devices';
		taxonomy.options.yaxis.ticks = [
			[0, ""], 
			[10, "Memristor"],
			[20, "Nanomagnet"],
			[30, "Solar Cell"],
			[40, "PN Junctions"], 
			[50, "MOSCAP"], 
			[60, "MOSFET"],
			[70, "BJT"],
			[80, "MESFET"],
			[90, "FinFET"],
			[100, "HFET"],
			[110, "nanoFET"],
			[120, "nanowire"],
			[130, "CNT"],
			[140, "GNR"],
			[150, "Superlattice"],
			[160, "Heterostr."],
			[170, "Res. Tun. Diodes"],
			[180, "Quantum Dot"],
			[190, "Molecules"],
			[200, "Tri./Coul. Well"], 
			[210, "Square Well"], 
			[220, "Harmonic Pot."],
			[230, "Fullerene"], 
			[240, "Bulk"],
			[250, "Crystals"]
		];
		taxonomy.options.yaxis.max = 250;
		
		taxonomy.levels = [
			[0,  10, false, "freshman", "#39b54a"],
			[10, 20, false, "sophmore", "#39b54a"],
			[20, 30, false, "junior", "#00aeef"], //#1b75bc
			[30, 40, false, "senior", "#00aeef"], //#1b75bc
			[40, 50, false, "ms", "#0d3f89"], //#1b75bc
			[50, 60, false, "phd", "#231f20"]
		];

		//[x, y, span, title, active, url, audience level start, audience level end, magnitude, NCN supported]
		taxonomy.datasource = [
			[55, 65, 10, "BJT", 0, "/tools/bjt", 25, 35, 10, true], // 6
			[58, 235, 10, "Bulk Silicon Data", 0, "/tools/bulkmobility", 35, 35, 10, false], // 86
			[52, 233, 10, "Low Field Mobility", 0, "/tools/ifmobility", 35, 35, 10, false], // 95
			[42, 235, 10, "Mont Carlo Transport", 0, "/tools/moca-ensemble", 45, 45, 10, false], // 57
			[45, 235, 10, "Bulk Monte Carlo", 0, "/tools/bulkmc", 45, 45, 10, false], // 58
			[32, 205, 30, "Kronig-Penney", 0, "/tools/kronig_penney", 45, 45, 10, true], // 32
			[32, 235, 10, "Kronig-Penney", 0, "/tools/kronig_penney", 45, 45, 10, true], // 32
			[35, 205, 30, "Periodic Potential", 0, "/tools/periodicpot", 45, 45, 10, false], // 129
			[35, 235, 10, "Periodic Potential", 0, "/tools/periodicpot", 45, 45, 10, false], // 129
			[33, 145, 10, "Periodic Potential", 0, "/tools/periodicpot", 45, 45, 10, false], // 129
			[105, 126, 10, "CNT Interconnect", 0, "/tools/cnia", 45, 55, 10, false], // 60
			[55, 125, 10, "CNTmobility", 0, "/tools/cntmob", 45, 55, 10, false], // 64
			[52, 125, 10, "Schottky_CNT", 0, "/tools/sbcnfet", 45, 45, 10, false], // 147
			[58, 125, 10, "CNT-MOSFET", 0, "/tools/moscntr", 45, 55, 10, false], // 74
			[39, 122, 10, "CNT-MOSFET", 0, "/tools/moscntr", 45, 55, 10, false], // 74
			[45, 125, 10, "CNT-BTE", 0, "/tools/cntbte", 45, 45, 10, true], // 7
			[34, 122, 10, "CNTfet", 0, "/tools/cntfet", 45, 55, 10, false], // 68
			[87, 125, 10, "nanoJoule", 0, "/tools/swntjiv", 45, 55, 10, true], // 27
			[37, 125, 30, "VIDES", 0, "/tools/vides", 45, 55, 10, false], // 121
			[15, 130, 20, "CNTBands", 0, "/tools/cntbands-ext", 25, 55, 10, true], // 13
			[75, 120, 20, "nanoNET", 0, "/tools/nanonet", 55, 55, 10, false], // 120
			[67, 240, 20, "Semiconductor Fundamentals", 0, "/tools/abacus", 25, 55, 10, true], // 1
			[65, 50, 40, "Semiconductor Fundamentals", 0, "/tools/abacus", 25, 55, 10, true], // 1
			[63, 35, 10, "Semiconductor Fundamentals", 0, "/tools/abacus", 25, 55, 10, true], // 1
			[63, 240, 20, "Semiconductor Fundamentals", 0, "/tools/deviceelectron", 55, 55, 10, true], // 19
			[67, 60, 20, "Semiconductor Fundamentals", 0, "/tools/deviceelectron", 55, 55, 10, true], // 19
			[67, 35, 10, "Semiconductor Fundamentals", 0, "/tools/deviceelectron", 55, 55, 10, true], // 19
			[32, 85, 10, "Trigate Electrostatics", 0, "/tools/mctrigate", 45, 45, 10, false], // 142
			[52, 85, 10, "Trigate Electrostatics", 0, "/tools/mctrigate", 45, 45, 10, false], // 142
			[105, 135, 10, "GNR Interconnect", 0, "/tools/gnrinterconnect", 45, 55, 10, false], // 81
			[38, 210, 20, "Quantum Bond State", 0, "/tools/electromat", 35, 45, 10, false], // 143
			[35, 150, 20, "Tunneling Lab", 0, "/tools/pcpbt", 45, 45, 10, true], // 33
			[25, 155, 10, "Spin Precession", 0, "/tools/spinprecession", 45, 55, 10, false], // 49
			[85, 155, 10, "ThrmoSuperLatt", 0, "/tools/slpf", 45, 45, 10, false], // 165
			[38, 155, 10, "Quantum Dot Lab", 0, "/tools/qdot", 25, 25, 10, true], // 37
			[35, 175, 10, "Quantum Dot Lab", 0, "/tools/qdot", 25, 25, 10, true], // 37
			[105, 5, 10, "Memristor", 0, "/tools/memristor", 45, 45, 10, false], // 101
			[55, 75, 10, "MESFET", 0, "/tools/mesfet", 45, 45, 10, false], // 102
			[35, 185, 10, "MOLCtoy", 0, "/tools/molctoy", 35, 45, 10, false], // 108
			[53.5, 47, 10, "MOScap", 0, "/tools/moscap", 25, 45, 10, true], // 22
			[32, 45, 10, "MOSCAP quantum", 0, "/tools/cgtb", 35, 45, 10, false], // 62
			[35, 45, 10, "Schred", 0, "/tools/schred", 45, 45, 10, false], // 148
			[108, 55, 10, "nanoCMOS", 0, "/tools/nanocmos", 45, 45, 10, false], // 113
			[102, 53, 10, "SPICE", 0, "/tools/spice3f4", 15, 15, 10, false], // 154
			[53, 55, 10, "MOSFET", 0, "/tools/mosfet", 25, 45, 10, true], // 23
			[75, 55, 10, "Reliability", 0, "/tools/devrel", 45, 55, 10, false], // 76
			//[55, 55, 10, "Reliability", 0, "/tools/devrel", 45, 55, 10, false], // 76
			[45, 55, 10, "Monte Carlo Transport", 0, "/tools/archimedes", 55, 55, 10, false], // 51
			[42, 55, 10, "MOCA", 0, "/tools/moca", 35, 45, 10, false], // 87
			[48, 55, 10, "SMC", 0, "/tools/smc", 55, 55, 10, false], // 153
			[51.5, 45, 30, "PADRE", 0, "/tools/padre", 45, 55, 10, false], // 127
			[55, 45, 30, "TCAD", 0, "/tools/atcadlab", 45, 55, 10, false], // 46
			[58.5, 45, 30, "Medici", 0, "/tools/medici", 45, 45, 10, false], // 99
			[105, 56, 10, "PETE", 0, "/tools/pete", 45, 45, 10, false], // 130
			[103, 105, 10, "PETE", 0, "/tools/pete", 45, 45, 10, false], // 130
			[33, 55, 10, "nanoFET", 0, "/tools/nanofet", 45, 55, 10, false], // 130
			[37, 105, 10, "nanoFET", 0, "/tools/nanofet", 45, 55, 10, false], // 130
			[91, 50, 40, "ConDepDiffusion", 0, "/tools/prolabcdd", 25, 45, 10, false], // 134
			[92.5, 50, 40, "Oxidation", 0, "/tools/prolabox", 25, 45, 10, false], // 135
			[95.5, 50, 40, "Oxidationflux", 0, "/tools/prolaboxflux", 25, 45, 10, false], // 136
			[97.5, 50, 40, "PointDefectDiff", 0, "/tools/prolabdcd", 25, 45, 10, false], // 137
			[94, 50, 40, "Prophet", 0, "/tools/prophet", 45, 45, 10, false], // 138
			[99, 50, 40, "SUPREM", 0, "/tools/tsuprem4", 45, 45, 10, false], // 166
			[85, 245, 10, "ThermoCrystals", 0, "/tools/nccpf", 45, 45, 10, false], // 164
			[25, 15, 10, "ClusterMagnets", 0, "/tools/nanomgnets", 55, 55, 10, false], // 63
			[39, 105, 10, "Nanomos", 0, "/tools/nanomos", 45, 55, 10, false], // 119
			[55, 105, 10, "Nanomos", 0, "/tools/nanomos", 45, 55, 10, false], // 119
			[45, 105, 10, "QuantumMC2d", 0, "/tools/quamc2d", 45, 45, 10, false], // 141
			//[34, 105, 10, "OMEN_FET", 0, "/tools/omenhfet", 45, 55, 10, false], // 125
			[34, 100, 20, "OMEN_FET", 0, "/tools/omenhfet", 45, 55, 10, false], // 125
			[14, 115, 10, "OMENwire", 0, "/tools/omenwire", 55, 55, 10, true], // 31
			[52, 115, 10, "nanowireDriftDiff", 0, "/tools/nanowireclassic", 45, 45, 10, false], // 139
			[32, 115, 10, "nanowire", 0, "/tools/nanowire", 45, 55, 10, false], // 122
			[35, 115, 10, "nanowireMG", 0, "/tools/nanowireMG", 45, 55, 10, false], // 123
			[55, 115, 10, "MuGFET", 0, "/tools/nanofinfet", 35, 45, 10, false], // 111
			[57, 85, 10, "MuGFET", 0, "/tools/nanofinfet", 35, 45, 10, false], // 111
			[38.5, 115, 10, "nanowire_Finfet", 0, "/tools/kpnanofet", 45, 55, 10, false], // 94
			[37, 85, 10, "nanowire_Finfet", 0, "/tools/kpnanofet", 45, 55, 10, false], // 94
			[59, 38, 10, "PN Junction", 0, "/tools/pnlongbasedda", 35, 35, 10, true], // 20
			[58, 33, 10, "PN Junction", 0, "/tools/pntoy", 25, 45, 10, true], // 34
			[53, 38, 10, "NP Junction (long base)", 0, "/tools/nplongbasedda", 35, 35, 10, false], // 89
			[51, 35, 10, "NP Junction (short base)", 0, "/tools/npshortbasedda", 35, 35, 10, false], // 89
			[56, 37, 10, "PN Junction (short base)", 0, "/tools/pnshortbasedda", 35, 35, 10, false], // 91
			[25, 175, 10, "SpinCoupled Quantum Dots", 0, "/tools/spincoupleddots", 55, 55, 10, false], // 155
			[37, 175, 10, "Coulomb Blockade", 0, "/tools/coulombism", 45, 55, 10, false], // 72
			[5, 175, 10, "Coulomb Blockade", 0, "/tools/coulombism", 45, 55, 10, false], // 72
			[37, 203, 10, "Bound States", 0, "/tools/bsclab", 45, 45, 10, false], // 56
			[33, 170, 20, "Computational Electronics Class", 0, "/topics/acute", 45, 55, 10, false], // 47
			[33.5, 207, 10, "Computational Electronics Class", 0, "/topics/acute", 45, 55, 10, false], // 47
			[31, 170, 20, "Quantum Mechanics for Engineers", 0, "/topics/aqme", 45, 55, 10, false], // 47
			[33.5, 202, 10, "Quantum Mechanics for Engineers", 0, "/topics/aqme", 45, 55, 10, false], // 47
			[35, 164, 10, "Resonant Tunneling", 0, "/tools/rtdnegf", 45, 55, 10, true], // 39
			[37.5, 163, 10, "RTD", 0, "/tools/rtd", 35, 45, 10, false], // 146
			[34, 166, 10, "Heterostructure Design", 0, "/tools/1dhetero", 45, 55, 10, false], // 44
			[37, 95, 10, "Heterostructure Design", 0, "/tools/1dhetero", 45, 55, 10, false], // 44
			[32, 155, 10, "Heterostructure Design", 0, "/tools/1dhetero", 45, 55, 10, false], // 44
			[54, 30, 20, "Solar Cells", 0, "/tools/adept", 35, 45, 10, false], // 48
			//[54, 42, 10, "Solar Cells", 0, "/tools/adept", 35, 45, 10, false], // 48
			[55, 235, 10, "Drift-Diffusion", 0, "/tools/semi", 25, 45, 10, true], // 16
			[62, 233, 10, "Carrier Statistics", 0, "/tools/fermi", 25, 45, 10, true], // 10
			[65, 233, 10, "Doping", 0, "/tools/dopingsilicon", 25, 45, 10, true], // 17
			[45, 155, 10, "Demons", 0, "/tools/demons", 45, 55, 10, false], // 75
			[32, 127, 10, "FETtoy", 0, "/tools/fettoy", 45, 45, 10, false], // 77
			[32, 105, 10, "FETtoy", 0, "/tools/fettoy", 45, 45, 10, false], // 77
			[13, 235, 10, "Bandstructure", 0, "/tools/strainbands", 45, 55, 10, true], // 40
			[82.5, 125, 10, "CNTphonons", 0, "/tools/cntphonons", 45, 55, 10, false], // 69
			[12, 120, 20, "MaterialScienceLab", 0, "/tools/msl", 35, 45, 10, false], // 110
			[12, 225, 10, "MaterialScienceLab", 0, "/tools/msl", 35, 45, 10, false], // 110
			//[13, 115, 10, "MaterialScienceLab", 0, "/tools/msl", 35, 45, 10, false], // 110
			[17, 245, 10, "Crystal Viewer", 0, "/tools/crystal_viewer", 25, 45, 10, true], // 15
			[16, 225, 10, "Crystal Viewer", 0, "/tools/crystal_viewer", 25, 45, 10, true], // 15
			[18, 130, 20, "Crystal Viewer", 0, "/tools/crystal_viewer", 25, 45, 10, true], // 15
			[15, 185, 10, "Matdcal", 0, "/tools/matdcal", 45, 55, 10, false], // 97
			[17, 110, 20, "Bandstructure Lab", 0, "/tools/bandstrlab", 45, 55, 10, true], // 4
			[18, 235, 10, "Bandstructure Lab", 0, "/tools/bandstrlab", 45, 55, 10, true], // 4
			[68, 233, 10, "Bandstructure Lab", 0, "/tools/bandstrlab", 25, 45, 10, true], // 4
			[39, 170, 20, "Survey Course", 0, "/tools/antsy", 25, 25, 10, false], // 52
			[35, 127, 10, "Survey Course", 0, "/tools/antsy", 25, 25, 10, false], // 52
			[38, 206, 10, "Survey Course", 0, "/tools/antsy", 25, 25, 10, false], // 52
			[85, 235, 10, "SEST", 0, "/tools/sest", 45, 45, 10, false], // 149
			[45, 180, 20, "Path Integral Monte Carlo", 0, "/tools/pimc", 55, 55, 10, false], // 3690
			[45, 215, 10, "Path Integral Monte Carlo", 0, "/tools/pimc", 55, 55, 10, false], // 3690
			[15, 240, 20, "ABINIT", 0, "/tools/abinit", 55, 55, 10, true] // 3690
		];
		
		taxonomy.options.grid.labelMargin = 5;
		taxonomy.options.xaxis.labelsAngle = 90;
		taxonomy.options.shadowSize = 4;
		taxonomy.options.mouse.radius = 4;
		taxonomy.bgLevels = false;
		taxonomy.chart = 'dotz';
		taxonomy.extension = 'vert';
		
		f = Flotr.draw($('container'), [{data:taxonomy.datasource, dotz:{show:true,bgLevels:taxonomy.bgLevels,extension:taxonomy.extension}, barz:{show:false}}], taxonomy.options);
		taxonomy.setup();
	});
	</script>
	<?php
	$ids = array('bjt',
	'bulkmobility',
	'lfmobility',
	'moca-ensemble',
	'bulkmc',
	'kronig_penney',
	'periodicpot',
	'cnia',
	'cntmob',
	'sbcnfet',
	'moscntr',
	'cntbte',
	'cntfet',
	'swntjiv',
	'vides',
	'cntbands-ext',
	'nanonet',
	'abacus',
	'deviceelectron',
	'mctrigate',
	'gnrinterconnect',
	'electromat',
	'pcpbt',
	'spinprecession',
	'slpf',
	'qdot',
	'memristor',
	'mesfet',
	'molctoy',
	'moscap',
	'cgtb',
	'schred',
	'nanocmos',
	'spice3f4',
	'mosfet',
	'devrel',
	'archimedes',
	'moca',
	'smc',
	'padre',
	'atcadlab',
	'medici',
	'pete',
	'nanofet',
	'prolabcdd',
	'prolabox',
	'prolaboxflux',
	'prolabdcd',
	'prophet',
	'tsuprem4',
	'nccpf',
	'nanomgnets',
	'nanomos',
	'quamc2d',
	'omenhfet',
	'omenwire',
	'nanowireclassic',
	'nanowire',
	'mgnanowirefet',
	'nanofinfet',
	'kpnanofet',
	'pnlongbasedda',
	'pntoy',
	'nplongbasedda',
	'npshortbasedda',
	'pnshortbasedda',
	'spincoupleddots',
	'coulombsim',
	'bsclab',
	'acute',
	'aqme',
	'rtdnegf',
	'rtd',
	'1dhetero',
	'adept',
	'semi',
	'fermi',
	'dopingsilicon',
	'demons',
	'fettoy',
	'strainbands',
	'cntphonons',
	'msl',
	'crystal_viewer',
	'matdcal',
	'bandstrlab',
	'antsy',
	'sest',
	'pimc',
	'abinit');
	$sql = "SELECT C.id, C.title, C.type, C.introtext, C.fulltxt, C.created, C.created_by, C.modified, C.published, C.publish_up, C.standalone, C.hits, C.rating, C.times_rated, C.params, C.alias, C.ranking, t.type AS typetitle, lt.type AS logicaltitle 
		FROM #__resources AS C 
		LEFT JOIN #__resource_types AS t ON C.type=t.id 
		LEFT JOIN #__resource_types AS lt ON C.logical_type=lt.id 
		WHERE C.published=1 AND C.standalone=1 AND LOWER(C.alias) IN ('".implode("','",$ids)."')";
	?>
<?php } ?>
<?php
	$database = App::get('db');
	$database->setQuery($sql);
	$results = $database->loadObjectList();

	if ($results)
	{
		include_once(PATH_CORE.DS.'components'.DS.$this->option.DS.'models'.DS.'resource.php');

		//$rt = new \Components\Resources\Models\Tags($database);

		$xtra_tag = '';
		if ($this->config->get('supportedtag'))
		{
			$tag = \Components\Tags\Models\Tag::oneByTag($this->config->get('supportedtag'));

			$sl = $this->config->get('supportedlink');
			if ($sl)
			{
				$link = $sl;
			}
			else
			{
				$link = Route::url('index.php?option=com_tags&tag='.$tag->get('tag'));
			}

			$xtra_tag = '<p class="supported"><a href="'.$link.'">'.$tag->get('raw_tag').'</a></p>';
		}

		/*$levels = array();
		if ($this->config->get('show_audience'))
		{
			$ids = array();
			foreach ($results as $resource) 
			{
				$ids[] = $resource->id;
			}

			$sql = "SELECT a.* , L0.title as label0, L1.title as label1, L2.title as label2, L3.title as label3, L4.title as label4 , L0.description as desc0, L1.description as desc1, L2.description as desc2, L3.description as desc3, L4.description as desc4 
					FROM `#__resource_taxonomy_audience` AS a
					JOIN `#__resource_taxonomy_audience_levels` AS L0 
					on L0.label='level0' JOIN `#__resource_taxonomy_audience_levels` AS L1 
					on L1.label='level1' JOIN `#__resource_taxonomy_audience_levels` AS L2 
					on L2.label='level2' JOIN `#__resource_taxonomy_audience_levels` AS L3 
					on L3.label='level3' JOIN `#__resource_taxonomy_audience_levels` AS L4 
					on L4.label='level4' 
					WHERE a.rid IN (" . implode(",", $ids) . ")";
			$database->setQuery($sql);
			if ($audiencelevels = $database->loadObjectList())
			{
				foreach ($audiencelevels as $level)
				{
					$levels[$level->rid] = $level;
				}
			}
		}*/

		foreach ($results as $resource) 
		{
			$statshtml = '';
			// Get parameters and merge with the component params
			$rparams = new \Hubzero\Config\Registry($resource->params);
			$params = $this->config;
			$params->merge($rparams);

			// Generate the SEF
			$sef = Route::url('index.php?option=' . $this->option . ($resource->alias ? '&alias=' . $resource->alias : '&id=' . $resource->id));

			// Get resource helper
			$helper = new \Components\Resources\Helpers\Helper($resource->id, $database);
			//$helper->getFirstChild();

			$statshtml = '';
			if ($params->get('show_ranking'))
			{
				$helper->getLastCitationDate();
				if ($resource->type == 7)
				{
					$stats = new \Components\Resources\Helpers\Usage\Tools($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
					$s = $stats->display();
					if ($stats->users != 'unavailable')
					{
						$statshtml .= '<p class="usage">'.$stats->users.' Users</p>'."\n";
					}
					if ($stats->jobs != 'unavailable')
					{
						$statshtml .= '<p class="usage">'.$stats->jobs.' Jobs</p>'."\n";
					}
					if ($stats->avg_exec != 'unavailable')
					{
						$statshtml .= '<p class="usage"><abbr title="Average">Avg.</abbr> <abbr title="execution">exec.</abbr> time: '.$stats->valfmt($stats->avg_exec).'</p>'."\n";
					}
				}
				else
				{
					$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $resource->id, $resource->type, $resource->rating, $helper->citationsCount, $helper->lastCitationDate);
					$s = $stats->display();
					if ($stats->users != 'unavailable')
					{
						$statshtml .= '<p class="usage">'.$stats->users.' Users</p>'."\n";
					}
				}
			}

			/*$supported = null;
			if ($this->config->get('supportedtag'))
			{
				$supported = $rt->checkTagUsage($this->config->get('supportedtag'), $resource->id);
			}
			$xtra = '';

			if ($params->get('show_audience') && isset($levels[$resource->id]))
			{
				include_once(PATH_CORE.DS.'components'.DS.$this->option.DS.'tables'.DS.'audience.php');
				include_once(PATH_CORE.DS.'components'.DS.$this->option.DS.'tables'.DS.'audiencelevel.php');

				//$ra = new \Components\Resources\Tables\Audience($database);
				//$audience = $ra->getAudience($resource->id, 0, 1, 4);
				$audience = $levels[$resource->id];

				$xtra .= \Components\Resources\Helpers\Html::showSkillLevel($audience, 0, 4, $params->get('audiencelink'));
			}
			if ($this->config->get('supportedtag') && $supported)
			{
				$xtra .= $xtra_tag;
			}*/
			// Get the sections
			$sections = array(array('metadata'=>$statshtml)); //Event::trigger( 'resources.onResources', array($resource, $this->option, array('about'), 'metadata') );
?>
		<div id="r-<?php echo ($resource->alias) ? strtolower($resource->alias) : $resource->id; ?>" class="hide">
			<h4><a href="<?php echo $sef; ?>"><?php echo stripslashes($resource->title); ?></a></h4>
			<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($resource->introtext), 250); ?></p>
			<?php
			if ($params->get('show_metadata'))
			{
				//echo \Components\Resources\Helpers\Html::metadata($params, $resource->ranking, $statshtml, $resource->id, $sections, $xtra);
				echo $statshtml;
			}
			?>
		</div><!-- / .hide #r<?php echo $resource->id; ?> -->
<?php
		}
	}
?>
</div><!-- / .main section #taxonomy-section -->
