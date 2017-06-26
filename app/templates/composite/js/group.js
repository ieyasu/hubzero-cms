/**
 * @package     hubzero-cms
 * @file        templates/system/js/group.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}


HUB.template = {};

jQuery(document).ready(function(jq){
	var $ = jq;

	// group pane toggle
	$('#group a.toggle, #group-info .close').on('click', function(event) {
		event.preventDefault();

		$('#group-info').slideToggle('normal');
		$('#group-body').toggleClass('opened');
	});
	
});