/**
 * @package     hubzero-cms
 * @file        templates/hubbasic/js/globals.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
var HUB = HUB || {};

var alertFallback = true;
if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

//-----------------------------------------------------------
//  Various functions - encapsulated in HUB namespace
//-----------------------------------------------------------
if (!jq) {
	var jq = $;

	$.getDocHeight = function(){
		var D = document;
		return Math.max(Math.max(D.body.scrollHeight, D.documentElement.scrollHeight), Math.max(D.body.offsetHeight, D.documentElement.offsetHeight), Math.max(D.body.clientHeight, D.documentElement.clientHeight));
	};
} else {
	jq.getDocHeight = function(){
		var D = document;
		return Math.max(Math.max(D.body.scrollHeight, D.documentElement.scrollHeight), Math.max(D.body.offsetHeight, D.documentElement.offsetHeight), Math.max(D.body.clientHeight, D.documentElement.clientHeight));
	};
}

HUB.Base = {
	// Container for jquery.
	// Needed for noconflict mode compatibility
	jQuery: jq,

	// Set the base path to this template
	templatepath: '/app/templates/cdmhub/',

	// launch functions
	initialize: function() {
		var $ = this.jQuery, w = 760, h = 520;

		var menu = $('#top'),
			topSpacerTop = $('#top-spacer').offset().top;

		// Stick the toolbar to the top of the screen when the browser has scrolled
		$(window).on('scroll', function(event) {
			if(topSpacerTop > 0) {
				// what the y position of the scroll is
				var y = $(window).scrollTop();
				// whether that's below the form
				var t = (topSpacerTop - y);
				menu.css('top', (t < 0 ? 0 : t) + 'px');
			}
		});

		// Update the spacer's top offset value and reposition the toolbar
		$(window).on('resize', function(event) {
			topSpacerTop = $('#top-spacer').offset().top;
			$(this).scroll();
		});

		// Set focus on username field for login form
		if ($('#username').length > 0) {
			$('#username').focus();
		}

		// Turn links with specific classes into popups
		$('a').each(function(i, trigger) {
			if ($(trigger).is('.demo, .popinfo, .popup, .breeze')) {
				$(trigger).on('click', function (e) {
					e.preventDefault();

					if ($(this).attr('class')) {
						var sizeString = $(this).attr('class').split(' ').pop();
						if (sizeString && sizeString.match(/\d+x\d+/)) {
							var sizeTokens = sizeString.split('x');
							w = parseInt(sizeTokens[0]);
							h = parseInt(sizeTokens[1]);
						}
						else if(sizeString && sizeString == 'fullxfull')
						{
							w = screen.width;
							h = screen.height;
						}
					}

					window.open($(this).attr('href'), 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w);
				});
			}
			if ($(trigger).attr('rel') && $(trigger).attr('rel').indexOf('external') !=- 1) {
				$(trigger).attr('target', '_blank');
			}
		});

		// Set the overlay trigger for launch tool links
			$('.launchtool').on('click', function(e) {
				$.ajax({ url:HUB.Base.templatepath + 'images/anim/cssProgressBar.html' }).success(function(cssProgressBar) {
					$.fancybox({
									closeBtn: false,
									content: cssProgressBar 
								});
					
					var interval = 0;
					var oldFill = 0;
					setInterval(function() {
		  				var fill = 1.667 * interval;
		  				if (oldFill < 100){
		  					 $("#progressBar").css("width", fill+"%");
		  					 interval += 1;
		  					 oldFill = fill;
		  				}
		  			}, 1000);
				});
			});

		// $('.launchtool').on('click', function(e) {
		// 	$.fancybox({
		// 		closeBtn: false,
		// 		href: HUB.Base.templatepath + 'images/anim/cssProgressBar.html'   //circling-ball-loading.gif'
		// 	});
		// });

		// Set overlays for lightboxed elements
		$('a[rel=lightbox]').fancybox();

		// Init tooltips
		if (jQuery.ui && jQuery.ui.tooltip) {
			$(document).tooltip({
				items: '.hasTip, .tooltips',
				position: {
					my: 'center bottom',
					at: 'center top'
				},
				// When moving between hovering over many elements quickly, the tooltip will jump around
				// because it can't start animating the fade in of the new tip until the old tip is
				// done. Solution is to disable one of the animations.
				hide: false,
				content: function () {
					var tip = $(this),
						tipText = tip.attr('title');

					if (tipText.indexOf('::') != -1) {
						var parts = tipText.split('::');
						tip.attr('title', parts[1]);
					}
					return $(this).attr('title');
				},
				tooltipClass: 'tooltip'
			});

			// Init fixed position DOM: tooltips
			$('.fixedToolTip').tooltip({
				relative: true
			});
		}

		HUB.Base.placeholderSupport();

		$('#sub-masthead').prepend( '<div id="maintenance-note"> CDMHUB is currently undergoing a system upgrade.  <br> Tool usage will be limited during this time.  <br>We apologize for the inconvenience. </div>' );
		//$('#sub-masthead').prepend( '<div id="maintenance-note"> CDMHUB is currently experiencing network issues that are affecting tool functionality.<br> Our staff is aware of issue and working quickly to resolve.<br>We apologize for the inconvenience. </div>' );
	},

	placeholderSupport: function() {
		var $ = this.jQuery;

		//test for placeholder support
		var test = document.createElement('input'),
			placeholder_supported = ('placeholder' in test);

		//if we dont have placeholder support mimic it with focus and blur events
		if (!placeholder_supported) {
			$('input[type=text]:not(.no-legacy-placeholder-support)').each(function(i, el) {
				var placeholderText = $(el).attr('placeholder');

				//make sure we have placeholder text
				if (placeholderText != '' && placeholderText != null) {
					//add plceholder text and class
					if ($(el).val() == '') {
						$(el).addClass('placeholder-support').val(placeholderText);
					}

					//attach event listeners to input
					$(el)
						.on('focus', function() {
							if ($(el).val() == placeholderText) {
								$(el).removeClass('placeholder-support').val('');
							}
						})
						.on('blur', function(){
							if ($(el).val() == '') {
								$(el).addClass('placeholder-support').val(placeholderText);
							}
						});
				}
			});

			$('form').on('submit', function(event){
				$('.placeholder-support').each(function (i, el) {
					$(this).val('');
				});
			});
		}
	}
};

jQuery(document).ready(function($){
	HUB.Base.initialize();
});
