/**
 * @package     hubzero-cms
 * @file        templates/hubbasic2013/js/hub.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//  Create our namespace
if (typeof HUB === "undefined") {
	var HUB = {};
}
HUB.Base = {};

// Fallback support for browsers that don't have console.log
if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

// Support for jQuery noConflict mode
if (!jq) {
	var jq = $;
}

HUB.template = {};

// Let's get down to business...
jQuery(document).ready(function(jq) {
	var $ = jq,
		w = 760,
		h = 520,
		templatepath = '/app/templates/composite/';

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
					} else if (sizeString && sizeString == 'fullxfull') {
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

	if (jQuery.fancybox) {
		// Set the overlay trigger for launch tool links
		$('.launchtool').on('click', function(e) {
			$.fancybox({
				closeBtn: false,
				href: templatepath + 'images/anim/circling-ball-loading.gif'
			});
		});

		// Set overlays for lightboxed elements
		$('a[rel=lightbox]').fancybox();
	}

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

	/*
	 Template
	 */
	HUB.template.body = $('body');
	HUB.template.html = $('html');
	HUB.template.win = {};

	HUB.template.header = {};
	HUB.template.header.obj = $('header.main');
	HUB.template.header.mobile = false;
	HUB.template.header.allNav = $('.all-nav');
	HUB.template.header.subnav = $('.subnav');
	HUB.template.header.nav = $('.site-navigation');

	HUB.template.updateWin = function() {

		var wh = $(window).height();
		var ww = $(window).width();

		HUB.template.win.h = wh; // New height
		HUB.template.win.w = ww; // New width

		// Size-specific logic
		if(HUB.template.win.w >= 1200) {	//@sizeL
			HUB.template.screenSize = 'l';
		}
		else if(HUB.template.win.w > 900) {
			HUB.template.screenSize = 'm';
		}
		else {
			HUB.template.screenSize = 's';
		}

		if(HUB.template.win.w >= 888) {
			HUB.template.header.mobile = false;
		}
		else {
			HUB.template.header.mobile = true;
		}
	};

	// Switch to mobile mode when needed
	HUB.template.updateHeader = function() {
		// check for the collisions
		/*
		 var logoX = HUB.template.header.logo.offset().left;
		 var logoW = HUB.template.header.logo.outerWidth();

		 var navX = HUB.template.header.nav.offset().left;
		 var navW = HUB.template.header.nav.outerWidth();

		 var subnavnavX = HUB.template.header.subnav.offset().left;

		 var maxLogoGap = 0;
		 var maxSubnavGap = 30;

		 var logoGap = navX - (logoX + logoW);
		 var subnavGap = subnavnavX - (navX + navW);

		 if(logoGap < maxLogoGap || subnavGap < maxSubnavGap) {
		 HUB.template.header.mobile = true;
		 }
		 else {
		 HUB.template.header.mobile = false;
		 }
		 */
		if(!HUB.template.header.mobile) {
			// undo all mobile stuff
			HUB.template.header.allNav.attr('style', '');
		}
	};

	/*
	 // Search panel
	 */

	HUB.template.searchTrigger = $('header .subnav-search a');
	HUB.template.searchPanel = $('#big-search');
	HUB.template.searchField = $('#big-search #searchword');
	HUB.template.html = $('html');

	$(HUB.template.searchTrigger).on('click', function(e) {
		if (!(HUB.template.searchTrigger.hasClass('show'))) {
			HUB.template.closeAllPanels();
			HUB.template.openSearchPanel();
			// Disable input capturing for NoVNC
			if (typeof UI != 'undefined') {
				Util.removeEvent(document, 'click', UI.checkFocusBounds);
			}
		} else {
			HUB.template.closeAllPanels();
		}

		e.preventDefault();
	});

	$('#big-search .close').on('click', function(e) {
		HUB.template.closeAllPanels();

		// Restart input capturing for NoVNC
		if (typeof UI != 'undefined') {
			Util.addEvent(document, 'click', UI.checkFocusBounds);
		}

		e.preventDefault();
	});

	HUB.template.openSearchPanel = function() {
		HUB.template.body.addClass('panel-open');
		HUB.template.html.addClass('panel-open');
		HUB.template.searchTrigger.addClass('show');
		HUB.template.searchPanel.addClass('open');
		HUB.template.focus();
	};

	HUB.template.focus = function() {
		HUB.template.searchField.focus();
	};

	HUB.template.closeSearchPanel = function() {
		HUB.template.searchTrigger.removeClass('show');
		HUB.template.searchPanel.removeClass('open');
	};

	// Main Navigation Menu
	HUB.template.menuTrigger = $('.menu-button');
	HUB.template.menuPanel = $('.all-nav');
	HUB.template.menuCloseTrigger = HUB.template.menuPanel.find('.close');

	$(HUB.template.menuTrigger).on('click', function(e) {
		if(!(HUB.template.menuPanel.hasClass('open'))) {
			HUB.template.closeAllPanels();
			HUB.template.openMenuPanel();
		}
		else {
			HUB.template.closeAllPanels();
		}

		e.preventDefault();
	});

	$(HUB.template.menuCloseTrigger).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	HUB.template.openMenuPanel = function() {
		HUB.template.body.addClass('panel-open');
		HUB.template.html.addClass('panel-open');
		HUB.template.header.obj.addClass('panel-open');
		HUB.template.header.obj.addClass('display-nav');
		HUB.template.menuPanel.addClass('open');

		var tween = new TimelineMax({});

		tween
			.to(HUB.template.header.allNav, 0, {display: 'block', x: '-100%'})
			.to(HUB.template.header.allNav, 0.2, {x: '0%'});
		;
	};

	HUB.template.closeMenuPanel = function() {
		if(!HUB.template.menuPanel.hasClass('open')) {
			return;
		}

		HUB.template.menuPanel.removeClass('open');

		var tween = new TimelineMax({onComplete: function() {HUB.template.header.obj.removeClass('display-nav')}});

		tween
			.to(HUB.template.header.allNav, 0.2, {x: '-100%'});
		;
	};

	// Escape button to the rescue for those who like to press it in a hope to close whatever is open
	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			HUB.template.closeAllPanels();
		}
	});

	HUB.template.closeAllPanels = function() {
		HUB.template.closeSearchPanel();
		HUB.template.closeMenuPanel();

		if ($('#help-pane').length) {
			$('#help-pane').removeClass('open');
			if (typeof UI != 'undefined') {
				if ($('#session').length) {
					// If a session is running, it was pushed out of the way
					// as it can interfere with click events. Push it back.
					$('#session').css('margin-left', 'auto');
				}
				Util.addEvent(document, 'click', UI.checkFocusBounds);
			}
		}

		HUB.template.body.removeClass('panel-open');
		HUB.template.html.removeClass('panel-open');
		HUB.template.header.obj.removeClass('panel-open');

	};

	HUB.template.overlay = $('.hub-overlay');
	$(HUB.template.overlay).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	$(window).resize(function() {
		HUB.template.resize();
	});

	HUB.template.housekeeping = function() {
		var extra = $('#content-header-extra');
		if((extra.find('> *')).length == 1 && (extra.find('ul')).length) {
			if(!(extra.find('ul li')).length) {
				extra.remove();
			}
		}

		// find all headings followed by introduction
		var contentHeader = $('#content-header + #introduction, .content-header + #introduction');
		if(contentHeader.length == 1) {
			$('#content-header, .content-header').addClass('with-intro');
		}

		// relocate widowed content-header-extras and move them to the right parent
		var ch = $('#content-header, .content-header');

		if(ch.length && extra.length) {
			var insideCH = extra.closest('#content-header, .content-header');

			if(!contentHeader.length) {
				extra.detach().appendTo(ch);
			}
		}

		// relocate aside + subject headers
		var sHead = $('.section .subject .content-header');

		if(sHead.length) {
			var content = $('#content .content');
			sHead.detach().prependTo(content);
		}

	};

	// ---------------------------------
	// Sticky header business
	var scrolled = false;
	var lastScrollVal = 0;
	var page = $(this);
	var nav = HUB.template.header.obj;

	$(window).scroll(function(e) {
		scrolled = true;
	});

	HUB.template.handleScroll = function(check) {
		var scrollVal = page.scrollTop();
		var navHeight = nav.outerHeight();

		if(nav.hasClass('panel-open')) {
			return;
		}

		if(check && Math.abs(lastScrollVal - scrollVal) <= 5)
		{
			return;
		}

		if (scrollVal > lastScrollVal) {
			// Scroll Down
			if(scrollVal > (10)) {
				nav.addClass('out');
			}
		}
		else {
			// Scroll Up
			nav.removeClass('out');

			if(scrollVal < 10) {
				nav.addClass('on-top');
			}
			else {
				nav.removeClass('on-top');
			}
		}

		lastScrollVal = scrollVal;
	};

	setInterval(function() {
		if (scrolled) {
			HUB.template.handleScroll(true);
			scrolled = false;
		}
	}, 250);
	// ---------- end sticky header --------

	// Equalizer
	HUB.template.eq = function() {
		// Equalize
		$('.equalized').css('padding-top', '').removeClass('equalized');

		if(HUB.template.screenSize == 'l') { // @sizeL
			var eq = $('.eq');
			$.each(eq, function (k, v) {
				var em = $(v).find('.panel > .content');

				var maxH = 0;
				$.each(em, function (kk, vv) {
					if ($(vv).outerHeight() > maxH) {
						maxH = $(vv).outerHeight();
						//console.log(maxH);
					}
				});

				//console.log('---');

				$.each(em, function (kk, vv) {
					c = $(vv);
					var hDiff = maxH - c.outerHeight();
					if (hDiff > 0) {
						c.closest('.panel').addClass('equalized').css('padding-top', hDiff / 2);
					}
				});
			});
		}
	};

	// Move some headers
	HUB.template.moveHeader = function() {
		var moveHeader = $('header.header-move');

		if(moveHeader.length) {
			var moveTo = $('#content .inner .content');
			moveHeader.detach().addClass('content-header').prependTo(moveTo);
		}
	};

	// Fix the height of the page top background element
	HUB.template.updatePageTop = function() {
		var pageTopBg = $('.pageTop');
		var contentHeader = $('.content > .content-header, .content > #content-header, .content > .contentpane > .content-header');

		if(contentHeader.length) {
			var headerHeading = contentHeader.find('h2');
			var headerHeadingHtml = headerHeading.html();
			if (headerHeadingHtml.length > 66) {
				headerHeading.addClass('long');
			}
		}

		pageTopBg.height($('.trail').outerHeight() + contentHeader.outerHeight());
	};

	HUB.template.init = function() {
		HUB.template.moveHeader();
		HUB.template.handleScroll(false);
		HUB.template.resize();
		HUB.template.housekeeping();
	};

	HUB.template.resize = function() {
		if($(window).width() >= 1100) { // match the mobile breakpoint
			HUB.template.closeAllPanels();

			if (typeof HUB.template.homeResize == 'function') {
				//HUB.template.homeResize();
			}
		}
		HUB.template.updateWin();
		HUB.template.updatePageTop();
		HUB.template.updateHeader();
		HUB.template.eq();
	};

	HUB.template.init();

	// ***************************
	// temp preview function
	/*
	 $('.user-account-link').on('click', function(e) {
	 var body = $('body');
	 if(body.hasClass('in')) {
	 body.removeClass('in');
	 body.addClass('out');
	 $(this).html('Logged out view');
	 }
	 else {
	 body.removeClass('out');
	 body.addClass('in');
	 $(this).html('Logged in view');
	 }
	 HUB.template.init();
	 if (typeof HUB.template.homeUpdateCarousels == 'function') {
	 HUB.template.homeUpdateCarousels();
	 }
	 e.preventDefault();
	 });
	 */
	// ***************************
});

$(window).load(function() {
	// Recalculate dimension-dependent stuff since font loading changes things a bit
	HUB.template.resize();
});
