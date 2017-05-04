$(window).load(function() {
	var sliderAnimationSpeed = 1000;
	var allSlides = $('.slides > li');

	var slider = $('.flexslider');
	slider.flexslider({
		//slideshow: false,
		animation: "fade",
		animationSpeed: sliderAnimationSpeed,
		slideshowSpeed: 7000,
		controlNav: false,
		directionNav: false,
		start: function(slider) {
			$('.hero .loader-overlay').remove();
			//console.log('start fired');
			//startSlider(slider);
			playCurrentSlide(slider);
		},
		before: function(slider) {
			cleanCurrentSlide(slider);
		},
		after: function(slider) {
			//console.log('after fired');
			//console.log(slider.currentSlide);
			playCurrentSlide(slider);
		}
	});

	function nextSlide(slider) {
		slider.flexslider('play');
		//slider.next();
		//console.log(slider);
	}

	// SLIDES CUSTOM ANIMATION

	// slide one logic and animation
	var obj1 = {
		play: function() {
			//console.log('playing');
			window.setTimeout(function() {slider.flexslider('next'); slider.flexslider('play');}, 6000);
		},
		clean:  function() {
			//console.log('say b');
		}
	};

	// nano is HUGE slide logic -----------------------------------------------
	var slideHuge = {
		getReference: function() {
			var s = $('.slides .hero-dna');
			return s;
		},
		play: function() {
			// get the reference to the slide
			var s = this.getReference();
			s.addClass('in');
			window.setTimeout(function() {slider.flexslider('next'); slider.flexslider('play');}, 7000);
		},
		clean:  function() {
			var s = this.getReference();
			s.removeClass('in');
		}
	};
	// -----------------------------------------------

	// lookup obj
	var sliderLookup = {
		'nuke': obj1,
		'huge': slideHuge
	};

	function playCurrentSlide(s) {
		allSlides.removeClass('play');

		var currentSlide = $("li.flex-active-slide");
		// get the data-slide value from the active slide (if any)
		var slideAlias = slider.find(currentSlide).data('slide');

		if(typeof slideAlias != 'undefined') {
			// there is an alias, do the look up
			var animationObject = sliderLookup[slideAlias];

			if(typeof animationObject != 'undefined') {
				//console.log('Found animation object after lookup: ' + animationObject);
				slider.flexslider('pause');
				animationObject.play();
			}
		}
		else {
			currentSlide.addClass('play');
		}
	}

	function cleanCurrentSlide(s) {
		//console.log('Cleaning slide #' + s.currentSlide);

		// get the data-slide value from the active slide (if any)
		var slideAlias = slider.find("li.flex-active-slide").data('slide');
		//console.log('Alias is ' + slideAlias);

		if(typeof slideAlias != 'undefined') {
			// there is an alias, do the look up
			//console.log('Found alias: ' + slideAlias);

			var animationObject = sliderLookup[slideAlias];

			if(typeof animationObject != 'undefined') {
				//console.log('Found animation object after lookup: ' + animationObject);
				window.setTimeout(function() {animationObject.clean();}, sliderAnimationSpeed); //
			}
		}

		// if there is some special stuff, stop the slider play the slide
		if(false) {
			slider.flexslider('pause');
		}
	}


});

$(document).ready(function() {
	// Slider resizing

	HUB.template.homeResize = function() {
		HUB.template.homeSliderAdjust();
	};

	HUB.template.homeSliderAdjust = function() {
		//console.log(HUB.template.win);

		var setH = 500;

		if (HUB.template.win.h > setH) {
			setH = HUB.template.win.h;
		}

		$('.hero.flexslider').height(setH);
		$('.hero ul.slides > li').height(setH);
	};

	// ********************************************************
	// logo spin
	var lsdiv = $('.logospin div');

	if(lsdiv.length) {
		var iconDisplayTime = 1;
		var transitionTime = 1;
		var transitionOverlap = transitionTime;
		var tweenFrom = {x: -60, opacity: 0, scale: 0.1};
		var tweenShow = {x: 0, opacity: 0.5, scale: 1};
		var tweenTo = {x: 60, opacity: 0, scale: 0.1};

		// setup
		TweenMax.to(lsdiv, 0, tweenFrom);

		// create tween
		var lsTween = new TimelineMax({repeat: -1, delay: 0.5});

		// reference to the first element
		var firstIcon;

		lsdiv.each(function (index) {
			//return;
			//console.log( index + ": " + $( this ).text() );

			var icon = $(this);

			if (index == 0) {
				firstIcon = icon;

				// setup intro tween
				var initTween = new TimelineMax();
				initTween.to(firstIcon, transitionTime, tweenShow);

				lsTween
					.to(firstIcon, transitionTime, tweenTo, '+=' + iconDisplayTime)
					.to(firstIcon, 0, tweenFrom);
			}

			else {
				lsTween
					.to(icon, transitionTime, tweenShow, '-=' + transitionOverlap)
					.to(icon, transitionTime, tweenTo, '+=' + iconDisplayTime)
					.to(icon, 0, tweenFrom);
			}
		});

		// finish the first element to make a seamless transition
		lsTween.to(firstIcon, transitionTime, tweenShow, '-=' + transitionOverlap);
	}

	// Scroll
	var controller = new ScrollMagic.Controller();

	// ********************************************************
	// mission animation
	var logospin = $('.logospin');
	var mission = $('section.mission p');
	TweenMax.to(mission, 0, {y: -50, opacity: 0});
	TweenMax.to(logospin, 0, {opacity: 0});

	var missionTween = new TimelineMax();
	missionTween
			.to(mission, 1.5, {y: 0, opacity: 1})
			.to($('.logospin'), 1, {opacity: 1}, '-=1');

	new ScrollMagic.Scene({
		triggerElement: 'section.mission .content',
		triggerHook: 'onEnter',
		offset: "150"
	})
			.setTween(missionTween)
			.addTo(controller);


	// Slider set up
	$('.slides > li').addClass('out');

	HUB.template.homeInit = function() {
		HUB.template.homeSliderAdjust();
		HUB.template.homeResize();
	};

	HUB.template.homeInit();
});

$(window).resize(function() {
	HUB.template.homeResize();
});

$(window).load(function() {
	// Recalculate dimension-dependent stuff since font loading changes things a bit
	HUB.template.homeResize();
});