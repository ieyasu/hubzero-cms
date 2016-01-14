$(document).ready(function() {

  // Function definitions
  var transformSlider = function(name, scrollAmount, target) {
    $('.' + name + ' .scrollpane-inner-wrapper').attr('style', '' +
        '-webkit-transform:translateX(' + ( -100 * scrollAmount ) + '%);' +
        '-moz-transform:translateX(' + ( -100 * scrollAmount ) + '%);' +
        '-o-transform:translateX(' + ( -100 * scrollAmount ) + '%);' +
        'transform:translateX(' + ( -100 * scrollAmount ) + '%);' );
    $('.' + name + '-links>.active').removeClass('active');
    $(target).addClass('active');
  };


  // Onclick handlers
  $('.intro-links>button').on('click', function(e) {
    clearInterval(introInterval);
    transformSlider('intro', e.currentTarget.attributes['data-index'].value, e.currentTarget);
  });

  $('.resources-links>button').on('click', function(e) {
    clearInterval(resourcesInterval);
    transformSlider('resources', e.currentTarget.attributes['data-index'].value, e.currentTarget);
  });


  // Automatic transition
  var introIndex = 1,
      introButtons = $('.intro-links')[0].children,
      introInterval = setInterval(function() {
    if(introIndex >= introButtons.length) {
      introIndex = 0;
    }
    transformSlider('intro', introIndex, introButtons[introIndex++]);
  }, 10000);

  var resourcesIndex = 1,
      resourcesButtons = $('.resources-links')[0].children,
      resourcesInterval = setInterval(function() {
    if(resourcesIndex >= resourcesButtons.length) {
      resourcesIndex = 0;
    }
    transformSlider('resources', resourcesIndex, resourcesButtons[resourcesIndex++]);
  }, 10000);


  // Menu resizing
  $(document).on('scroll', function(e) {
    if($(window).scrollTop() > 10) {
      $('.menu-logo').addClass('small-logo');
    } else {
      $('.menu-logo').removeClass('small-logo');
    }
  });

  $('.menu-logo').removeClass('small-logo');
});
