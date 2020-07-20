/**
 * frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 */
jQuery(document).ready(function ($) {

    var yith_wcmg = $('.images');
    var yith_wcmg_zoom = $('.yith_magnifier_zoom');
    var yith_wcmg_image = $('.yith_magnifier_zoom img').first();

    var yith_wcmg_default_zoom = yith_wcmg.find('.yith_magnifier_zoom').attr('href');
    var yith_wcmg_default_image = yith_wcmg.find('.yith_magnifier_zoom img').attr('src');

    var yith_wcmg_default_gallery   = yith_wcmg.find('.thumbnails');


    if (typeof yith_magnifier_options == 'undefined') {
        return false;
    }

    yith_wcmg.yith_magnifier(yith_magnifier_options);

    $(document).on('found_variation', 'form.variations_form', function (event, variation) {
        var image_magnifier = variation.image_magnifier ? variation.image_magnifier : yith_wcmg_default_zoom;

        var image_src = yith_wcmg_default_image;
        if (ywzm_data.wc_before_3_0) {
            if (variation.image_src) {
                image_src = variation.image_src;
            }
        }
        else if (variation.image.src) {
            image_src = variation.image.src;
        }

        yith_wcmg_zoom.attr('href', image_magnifier);
        yith_wcmg_image.attr('src', image_src);
        yith_wcmg_image.attr('srcset', image_src);
        yith_wcmg_image.attr('src-orig', image_src);

        if (yith_wcmg.data('yith_magnifier')) {
            yith_wcmg.yith_magnifier('destroy');
        }

        yith_wcmg.yith_magnifier(yith_magnifier_options);
    }).on('reset_image', function (event) {

        yith_wcmg_zoom.attr('href', yith_wcmg_default_zoom);
        yith_wcmg_image.attr('src', yith_wcmg_default_image);
        yith_wcmg_image.attr('srcset', yith_wcmg_default_image);
        yith_wcmg_image.attr('src-orig', yith_wcmg_default_image);

        yith_wcmg.find('.thumbnails').replaceWith( yith_wcmg_default_gallery );

        if (yith_wcmg.data('yith_magnifier')) {
            yith_wcmg.yith_magnifier('destroy');
        }

        yith_wcmg.yith_magnifier(yith_magnifier_options);
    });

    if ( $( '.single-product.woocommerce div.product div.images ul.yith_magnifier_gallery' ).length ){

        $('form.variations_form .variations select').trigger('change');

        function yith_wc_zm_carousel(){

            $( ".single-product.woocommerce div.product div.images div.thumbnails" ).css( 'width', $( ".single-product.woocommerce div.product div.images div.thumbnails" ).width() );

            var yith_wc_zm_circular = $( '.single-product.woocommerce div.product div.images ul.yith_magnifier_gallery' ).data( 'circular' );
            var yith_wc_zm_columns = $( '.single-product.woocommerce div.product div.images ul.yith_magnifier_gallery' ).data( 'columns' );

            var slider_infinite = $( '.single-product.woocommerce div.product div.images ul.yith_magnifier_gallery' ).data( 'slider_infinite' );

            // We retrieve the value of the width of a li and the margin to later on multiple it for the numeber of li's and set the width of the ul parent
            var li_width = $( ".single-product.woocommerce div.product div.images .yith_magnifier_gallery li" ).width();
            var li_margin_left = $( ".single-product.woocommerce div.product div.images .yith_magnifier_gallery li" ).css( 'margin-left' );

            if ( typeof li_margin_left == 'undefined') {
                var li_margin_left_num = 0;
            } else {
                var li_margin_left_num = li_margin_left.split( "px" ).shift();
            }


            // We create an array with all the possible positions and adjust all the li's with a width width px because they were created by % via PHP
            // and we are goint to set the width of the ul which contains the li's
            var yith_margins_array = [];
            var yith_margins_option = 0;
            yith_margins_array.push( yith_margins_option );

            var loop = 1;
            $( '.single-product.woocommerce div.product div.images ul.yith_magnifier_gallery li' ).each( function() {

                $( this ).css( 'width', li_width + 'px' );
                $( this ).css( 'margin-left', li_margin_left );
                $( this ).css( 'margin-right', li_margin_left );
                $( this ).show();

                yith_margins_option = yith_margins_option + ( ( li_margin_left_num * 2 ) + li_width );

                yith_margins_array.push( yith_margins_option );

                loop++;

            });

            // set the width of the ul parent
            var ul_w = yith_margins_option;
            $( ".single-product.woocommerce div.product div.images .yith_magnifier_gallery" ).css( 'width', ul_w + 'px' );

            $( "#slider-next" ).css( 'top', '50%' );
            $( "#slider-next" ).css( 'transform', 'translateY( -50% )' );

            $( "#slider-prev" ).css( 'top', '50%' );
            $( "#slider-prev" ).css( 'transform', 'translateY( -50% )' );

            var index_yith_margins_array = 0;

            $( "body" ).on( "click", "#slider-next", function () {

                if ( $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).next().is( 'li' ) ){

                    index_yith_margins_array++;
                    $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).animate({
                        marginLeft: '-' + yith_margins_array[ index_yith_margins_array ] + 'px',
                    });

                    var next = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).next();
                    $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).removeClass( 'first' );
                    next.addClass( 'first' );

                    next = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).next();
                    $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).removeClass( 'last' );
                    next.addClass( 'last' );

                }
                else
                if ( ( yith_wc_zm_circular == 'yes' ) && ! $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).is(':animated') ){

                    index_yith_margins_array--;
                    $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).css( 'margin-left', '-' + yith_margins_array[ index_yith_margins_array ] + 'px' );

                    var last = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li' ).first();
                    $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).after( last );

                    $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).removeClass( 'last' );
                    last.addClass( 'last' );

                    var first = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).next();
                    $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).removeClass( 'first' );
                    first.addClass( 'first' );

                    index_yith_margins_array++;
                    $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).animate({
                        marginLeft: '-' + yith_margins_array[ index_yith_margins_array ] + 'px',
                    });

                }
                else
                if ( ( slider_infinite == 'yes' ) && ! $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).is(':animated') ){

                    while( $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).prev().is( 'li' ) ){

                        var prev = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).prev();
                        $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).removeClass( 'first' );
                        prev.addClass( 'first' );

                        prev = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).prev();
                        $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).removeClass( 'last' );
                        prev.addClass( 'last' );

                    }

                    index_yith_margins_array = 0;
                    $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).animate({
                        marginLeft: '-' + yith_margins_array[ index_yith_margins_array ] + 'px',
                    });
                }

            });

            $( "body" ).on( "click", "#slider-prev", function () {

                if ( $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).prev().is( 'li' ) ){

                    index_yith_margins_array--;
                    $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).animate({
                        marginLeft: '-' + yith_margins_array[ index_yith_margins_array ] + 'px',
                    });

                    var prev = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).prev();
                    $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).removeClass( 'first' );
                    prev.addClass( 'first' );

                    prev = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).prev();
                    $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).removeClass( 'last' );
                    prev.addClass( 'last' );

                }
                else
                if ( ( yith_wc_zm_circular == 'yes' ) && ! $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).is(':animated') ) {

                    index_yith_margins_array++;
                    $(".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery").css('margin-left', '-' + yith_margins_array[index_yith_margins_array] + 'px');

                    var first = $('.single-product.woocommerce div.product div.images .yith_magnifier_gallery li').last();
                    $('.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first').before(first);

                    $('.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first').removeClass('first');
                    first.addClass('first');

                    var last = $('.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last').prev();
                    $('.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last').removeClass('last');
                    last.addClass('last');

                    index_yith_margins_array--;
                    $(".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery").animate({
                        marginLeft: '-' + yith_margins_array[index_yith_margins_array] + 'px',
                    });

                }
                else
                if ( ( slider_infinite == 'yes' ) && ! $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).is(':animated') ){

                    while( $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).next().is( 'li' ) ){

                        var next = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).next();
                        $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.first' ).removeClass( 'first' );
                        next.addClass( 'first' );

                        next = $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).next();
                        $( '.single-product.woocommerce div.product div.images .yith_magnifier_gallery li.last' ).removeClass( 'last' );
                        next.addClass( 'last' );

                    }

                    index_yith_margins_array = yith_margins_array.length -1 -yith_wc_zm_columns;
                    $( ".single-product.woocommerce div.product div.images ul.yith_magnifier_gallery" ).animate({
                        marginLeft: '-' + yith_margins_array[ index_yith_margins_array ] + 'px',
                    });
                }

            });

        }

        function yith_wc_zm_auto_carousel(){

            $( '#slider-next' ).click();

            setTimeout( yith_wc_zm_auto_carousel, 3000);

        }

        yith_wc_zm_carousel();

        var auto_carousel = $( '.single-product.woocommerce div.product div.images ul.yith_magnifier_gallery' ).data( 'auto_carousel' );

        if ( auto_carousel == 'yes' )
            setTimeout( yith_wc_zm_auto_carousel, 3000);
    }

});