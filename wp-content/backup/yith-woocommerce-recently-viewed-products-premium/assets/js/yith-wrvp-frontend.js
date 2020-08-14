/**
 * frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

(function ($, window, document) {
    "use strict";

    $.fn.yith_wrvp_slider = function() {
        var items = $(this);
        
        items.each(function() {

            var is_slider = $(this).data('slider') == 1,
                autoplay = $(this).data('autoplay') == 1,
                autoplayspeed = $(this).data('autoplayspeed'),
                columns = $(this).data('numcolumns');

            if ( is_slider ) {
                $(this).find( ywrvp.products_selector ).not('.slick-initialized').slick({
                    infinite: true,
                    autoplay: autoplay,
                    speed: '300',
                    autoplaySpeed: autoplayspeed,
                    slidesToShow: columns,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1200,
                            settings: {
                                slidesToShow: columns - 1,
                                infinite: true
                            }
                        },
                        {
                            breakpoint: 767,
                            settings: {
                                slidesToShow: columns - 2
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            }
        });
    };

    if( typeof ywrvp != 'undefined' ) {
        // ajax filter by cat
        $('.yith-wrvp-filters-cat').on('click', 'a.cat-link', function (ev) {

            ev.preventDefault();

            var t = $(this),
                t_wrapper = $(this).closest('.filter-cat'),
                container = $( '.yith-similar-products'),
                nav = $('.woocommerce-pagination'),
                cat_id = t_wrapper.hasClass('active') ? '0' : t.data( 'cat_id' ),
                data = {
                    ywrvp_cat_id: cat_id,
                    context: 'frontend'
                };

            $.ajax({
                url: ywrvp.url,
                type: 'GET',
                data: data,
                dataType: 'html',
                success: function( res ) {

                    container.html( $(res).find('.yith-similar-products') );
                    nav.html( $(res).find( '.woocommerce-pagination' ) );

                    t_wrapper.toggleClass('active').siblings().removeClass('active');

                    $(document).trigger('yith-wrvp-product-changed');
                }

            })

        });
    }

    // START ON READY
    $( document ).ready( function(){
        $( document ).find( '.yith-similar-products' ).yith_wrvp_slider();
    });

})(jQuery, window, document);