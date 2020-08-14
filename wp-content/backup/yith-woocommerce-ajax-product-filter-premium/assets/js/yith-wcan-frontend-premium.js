/**
 * Frontend
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */
(function ($) {
    var wc_price_filter_button  = $('.price_slider_amount button');

    if( yith_wcan_frontend_premium.wc_price_filter_slider_in_ajax == 'yes' ) {
        wc_price_filter_button.remove();
    }

    else {
        wc_price_filter_button.show();
    }

    if (yith_wcan_frontend_premium.wc_price_filter_dropdown == 'yes') {
        var dropdown_span = '<span class="' + yith_wcan_frontend_premium.price_filter_dropdown_class + '" data-toggle="'+ yith_wcan_frontend_premium.wc_price_filter_dropdown_style + '"></span>';
        $('.widget_price_filter').find( yith_wcan_frontend_premium.wc_price_filter_dropdown_widget_class ).addClass('with-dropdown').append( dropdown_span );
    }

    var widget_dropdown = function () {

        var wcan_widgets  = $(document).find('.yith-woo-ajax-navigation').add('.yith-wcan-sort-by').add('.yith-wcan-stock-on-sale').add('.widget_price_filter').add('.yith-wcan-list-price-filter');
        if (wcan_widgets.length) {

            wcan_widgets.each(function () {

                var t = $(this),
                    title = t.find(yith_wcan_frontend_premium.wc_price_filter_dropdown_widget_class),
                    toggle = title.find('.widget-dropdown');


                if (toggle.length != 0) {
                    var dropdown_type = toggle.data('toggle'),
                        dropdown_classes = 'with-dropdown';

                    if (dropdown_type == 'open') {
                        dropdown_classes = dropdown_classes + ' open';
                    }

                    else {
                        title.closest(yith_wcan_frontend_premium.widget_wrapper_class).find('ul').hide();
                        title.closest(yith_wcan_frontend_premium.widget_wrapper_class).find('form').hide();
                    }

                    title.addClass(dropdown_classes);
                }

                title.off('click').on('click', function () {
                    var this_title = $(this);
                    if (this_title.find('.widget-dropdown').length != 0) {
                        this_title.toggleClass('open');
                        this_title.closest(yith_wcan_frontend_premium.widget_wrapper_class).find('ul').slideToggle( 'slow' );
                        this_title.closest(yith_wcan_frontend_premium.widget_wrapper_class).find('form').slideToggle( 'slow' );
                    }
                })
            })
        }
    };

    if( yith_wcan_frontend_premium.force_widget_init == 1 ){
        widget_dropdown();
    }

    $(document).on('ready yith-wcan-ajax-filtered', widget_dropdown);

    $(document).on('click', '.orderby-item, .yith-wcan-onsale-button, .yith-wcan-instock-button, .yith-wcan-price-link', function (e) {
        $(this).yith_wcan_ajax_filters(e, this);
    });

    if( yith_wcan_frontend_premium.ajax_wc_price_filter == 'yes' ){
        $(document).on('click', '.price_slider_amount button', function (e) {
            $(this).yith_wcan_ajax_filters(e, this);
        });
    }

    if( yith_wcan_frontend_premium.wc_price_filter_slider != 'yes' ) {
        var removePriceFilterSlider = function() {
            $( 'input#min_price').add('input#max_price').addClass('yith_wcan_no_slider').show();
            $('form > div.price_slider_wrapper').find( 'div.price_slider, div.price_label' ).hide();
        };

        $(document).on('ready', removePriceFilterSlider);
    }

    if( yith_wcan_frontend_premium.wc_price_filter_slider_in_ajax == 'yes' ) {
        $( '.price_slider').on( 'slidestop', function(e){
            $(this).yith_wcan_ajax_filters(e, this);
        });
    }

    if( yith_wcan_frontend_premium.ajax_pagination_enabled == 'yes' ){
        $(document).on('click', yith_wcan_frontend_premium.pagination_anchor, function (e) {
            $(this).yith_wcan_ajax_filters(e, this);
        });
    }
}(jQuery));
