<style type="text/css">
.outer {
	padding: 2em;
	margin-top: 0;
}
pre {
	display: none;
}
.logs .tabs li {
	display: inline-block;
}
.logs .tabs .current {
	background: #888;
	color: #fff;
}
.logs .download {
	display: none;
}
.status {
	display: inline-block;
	background: #900;
	width: 10px;
	height: 10px;
	border-radius: 7px;
	margin-right: 6px;
}
.status.ok {
	background: #090;
}
.update pre {
	max-height: 300px;
	overflow: auto;
}
</style>
<div class="outer">
	<h2>Lab Administration</h2>
	<h3>Actions</h3>
	<ul>
		<li class="update">
			<button class="btn"><span class="status ok"></span>Pull updates</button>
			<pre></pre>
		</li>
		<li class="http-server">
			<button class="btn"><span class="status ok"></span>Restart server</button>
			<pre></pre>
		</li>
	</ul>
	<h3>Information</h3>
	<div class="logs">
		<ul class="tabs">
			<li><button class="btn access-log">Access</button></li>
			<li><button class="btn resource-summary">Resource usage</button></li>
			<li><button class="btn csp-log">CSP violations</button></li>
		</ul>
		<pre></pre>
		<button class="btn download">Download</button>
	</div>
</div>
<script>
var base = '/labs/command/n4mics/';
var showServer = function() {
	$.get(base + 'server-status', function(resp) {
		$('.http-server pre').text(resp).show();
	});
};
showServer();

$.get(base + 'git-head', function(resp) {
	$('.update pre').text(resp).show();
});

var updater = null;
var getInfo = function(type) {
	return function() {
		$('.logs pre, .logs .download').hide();
		$('.logs .tabs .btn').removeClass('current');
		$(this).addClass('current');
		updater = type;
		var reup = function() {
			$.get(base + type, function(resp) {
				if (updater == type) {
					$('.logs pre').text(resp).show();
					$('.logs .download').show();
					setTimeout(reup, 1000);
				}
			});
		};
		reup();
	};
};
$('.access-log').click(getInfo('access-log'));
$('.resource-summary').click(getInfo('resource-summary'));
$('.csp-log').click(getInfo('csp-log'));

$('.http-server button').click(function() {
	$('.http-server .status').removeClass('ok');
	$('.http-server pre').hide();
	$.get(base + 'restart-server', function() {
		$('.http-server .status').addClass('ok');
		showServer();
	});
});

</script>
