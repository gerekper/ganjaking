(function ($) {

    "use strict";

    // WPBakery responsive settings
    responsiveEl();

    function responsiveEl() {

        var matches = document.querySelectorAll("[data-res-css]");
        var resdata = [];
        matches.forEach(function(element) {
            var get_style = element.getAttribute("data-res-css");
            resdata.push(get_style);
            element.removeAttribute("data-res-css");
        });

        var css = resdata.join(""),
        head = document.head || document.getElementsByTagName('head')[0],
        style = document.createElement('style');

        style.type = 'text/css';
        style.setAttribute("data-type", "rafia-shortcodes-custom-css");

        if (style.styleSheet){
            // This is required for IE8 and below.
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);

    }


    jQuery(document).ready(function( $ ) {

        // add class for bootstrap table
        $( "body:not(.woocommerce-page) .nt-theme-content table, #wp-calendar" ).addClass( "table table-striped" );

        // format gallery
        var post_gallery = $('.post-gallery-type').size();

        if(post_gallery){
            /* event-deatils-active */
            $('.post-gallery-type').owlCarousel({
                loop:true,
                nav:true,
                dots:false,
                autoplay:true,
                navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                responsive:{
                    0:{
                        items:1
                    },
                    576:{
                        items:1
                    },
                    768:{
                        items:1
                    },
                    992:{
                        items:1
                    },
                    1200:{
                        items:1
                    }
                }
            });
        }

        // vc_row parllax
        var parallaxbg= $('.nt-jarallax');

        if (parallaxbg > 0){

            $('.nt-jarallax').jarallax({

            });

            jarallax(document.querySelectorAll('.nt-jarallax.mobile-parallax-off'), {
                disableParallax: /iPad|iPhone|iPod|Android/,
                disableVideo: /iPad|iPhone|iPod|Android/
            });

        }

        // masonry

        var masonry_check = $('#masonry-container').size();

        if(masonry_check){

            //set the container that Masonry will be inside of in a var
            var container = document.querySelector('#masonry-container');
            //create empty var msnry
            var msnry;
            // initialize Masonry after all images have loaded
            imagesLoaded( container, function() {
                msnry = new Masonry( container, {
                    itemSelector: '.masonry-item'
                });
            });

        }

        // All animations will take exactly 500ms
        var scroll = new SmoothScroll('a[href*="#"]', {
            ignore: '[data-vc-accordion], #top-bar a',
            speed: 1000,
            topOnEmptyHash: true,
            clip: true,
            easing: 'easeInOutCubic',
            customEasing: function (time) {
                return time < 0.5 ? 2 * time * time : -1 + (4 - 2 * time) * time;
            },
            offset: function (anchor,toggle) {
                var offsett = $('body').hasClass('admin-bar') ? -32 : 0;
                return offsett;
            },
            updateURL: true,
            popstate: true,
            emitEvents: true
        });

        // CF7 remove error message
        $('.wpcf7-response-output').ajaxComplete(function(){

            window.setTimeout(function(){
                $('.wpcf7-response-output').addClass('display-none');
            }, 4000); //<-- Delay in milliseconds

            window.setTimeout(function(){
                $('.wpcf7-response-output').removeClass('wpcf7-validation-errors display-none');
            }, 4500); //<-- Delay in milliseconds

        });

        $('#nt-sidebar select, .woocommerce .woocommerce-ordering select').niceSelect();

    }); // end ready

    // preloader
    $(window).load(function () {

        // Animate loader off screen
        $(".se-pre-con").fadeOut("slow");

        $('#nt-preloader').fadeOut('slow', function () {
            $(this).remove();
        });

    });

})(jQuery);
