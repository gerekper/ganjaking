(function($) {
    'use strict';

    $(document).ready(function() {

        $('.rtwwdpd-carousel-slider').owlCarousel({
            loop: false,
            margin: 15,
            nav: false,
            navText: false,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                600: {
                    items: 2,
                    nav: false
                },
                1000: {
                    items: 4,
                    nav: true,
                    loop: false
                }
            }
        });
    })

})(jQuery);