<!doctype html>
<html>
<head>
	<title>Labs</title>
	<script src="/app/components/com_labs/media/js/iframeResizer.js"></script>

</head>
<body>
<style type="text/css">
html, body {
	height: 100%;
	width: 100%;
	margin: 0;
	padding: 0;
}
iframe {
	margin: 0;
	width: 100%;
	height: 100%;
	border: 0;
}
h2 {
	margin: 0;
	padding: 1em;
}
.labs.warning {
	clear: both;
	margin: 2em 1em;
	padding-top: 0;
	margin-top: 0;
	position: relative;
	top: 1em;
}
.btn.dismiss {
	float: right;
	background: rgba(255,255,255,0.6);
	border: 0;
	border-shadow: none;
	font-weight: bold;
}
.btn.dismiss tt {
	color: #333;
	margin-left: 1em;
}
h2.app {
	display: none;
}
#system-debug {
	display: none;
}
</style>
<iframe id="hubpub" src="<?php echo $a($connect); ?>" border=0></iframe>
<script>
iFrameResize({ 'targetOrigin': 'ojs.labs.cdmhub.aws.hubzero.org' });

function hubPubUpdateMeta(url, title, breadcrumbs) {
	window.scrollTo(0, 0);
	console.log('scroll up');

	var urlParser = document.createElement('a');
	urlParser.href = url;
	var ma = location.toString().match(/(\/labs\/run\/[-_0-9A-Za-z]+)/);
	var title = document.title.replace(/^(.*?Labs).*$/, function(_, prefix) { return prefix }) + (title ? ' - ' + title : '');
	document.title = title;
	if (history) {
		history.pushState({}, title, ma[1] + url);
	}
	breadcrumbs = JSON.parse(breadcrumbs);
	if (!breadcrumbs) {
		return; /// @TODO adding extraneous junk on error pages
		breadcrumbs = '<a href="/">' + $('h2.app').text() + '</a>';
	}
	breadcrumbs = '<a href="/resources/labs">Labs</a> › ' + breadcrumbs
	$('#trail .breadcrumbs').html(baseBC + ' › ' + breadcrumbs.replace('Home', $('h2.app').text()));
	$('#trail .breadcrumbs a').each(function(_, el) {
		var href = $(el).attr('href');
		if (href == '/resources/labs') {
			return;
		}
		$(el).attr('href', ma[1] + (/^\//.test(href) ? href : url + href));
	});
}

window.onpopstate = function(evt) {
	document.location = document.location;
};
</script>
</body>
</html>
