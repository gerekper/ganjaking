jQuery(document).ready(function($) {
    'use strict';
    /*
     Circle Slider
     */
    setTimeout(function() {
        if ($.fn.flipshow && window.Modernizr) {
            var circleContainer = $('.concept-slideshow');

            if (circleContainer.get(0)) {
                circleContainer.flipshow();

                setTimeout(function circleFlip() {
                    circleContainer.data().flipshow._navigate(circleContainer.find('div.fc-right span:first'), 'right');
                    setTimeout(circleFlip, 3000);
                }, 3000);
            }
        }
    }, 200);

    /*
     Move Cloud
     */
    if ($('.cloud').get(0)) {
        var moveCloud = function() {
            $('.cloud').animate({
                'top': '+=20px'
            }, 3000, 'linear', function() {
                $('.cloud').animate({
                    'top': '-=20px'
                }, 3000, 'linear', function() {
                    moveCloud();
                });
            });
        };

        moveCloud();
    }
});