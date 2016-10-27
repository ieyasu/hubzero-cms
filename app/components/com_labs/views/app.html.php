<script src="/app/components/com_labs/media/js/iframeResizer.js"></script>
<style type="text/css">
iframe {
	margin: 1em;
	width: 95%;
	overflow: none;
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
</style>
<h2 class="app"><?php echo $h($title); ?></h2>
<?php if (!isset($_COOKIE['labs-dismissed']) || $_COOKIE['labs-dismissed'] != 1): ?>
<div class="warning labs">
	<p>The material below is presented here on an experimental basis. It is not developed or maintained by the hubzero staff. If you experience any problems or have questions, <a>submit a support ticket</a> from this page and we'll do our best to route it to the developers of this lab.</p>
	<button class="btn dismiss">Got it <tt>x</tt></button>
</div>
<?php endif; ?>
<iframe id="hubpub" src="<?php echo $a($connect); ?>" border=0></iframe>
<script>
$('.warning.labs .btn.dismiss').click(function() {
	$(this).parent().hide('slow');
	document.cookie = "labs-dismissed=1; expires=0; path=/labs";
});
iFrameResize({ 'targetOrigin': 'n4mics.labs.aws.hubzero.org' });

var home = $('.breadcrumbs.pathway').find('span:nth-child(2)'), homeText = home.text();
home.empty();
home.append($('<a href="/">').text(homeText));
var baseBC = $('.breadcrumbs.pathway').html();
function hubPubUpdateMeta(url, title, breadcrumbs) {
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
