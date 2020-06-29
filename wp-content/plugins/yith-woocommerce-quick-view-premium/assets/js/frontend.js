/**
 * frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Quick View
 * @version 1.0.0
 */

jQuery(document).ready(function($){
    "use strict";

    if( typeof yith_qv == 'undefined' ){
        return;
    }

    var buttons     = '',
        qv_modal    = yith_qv.type != 'yith-inline' ? $(document).find( '.yith-quick-view' ) : $(document).find( '.yith-quick-view' ).clone(),
        qv_content  = qv_modal.find( '.yith-quick-view-content' ),
        qv_close    = qv_modal.find( '.yith-quick-view-close' ),
        qv_nav      = qv_modal.find( '.yith-quick-view-nav' ),
        products_ids = [],
        theSlider   = null,
        current_xhr = null,
        center_modal = function() {

            if( yith_qv.type == 'yith-inline' ) {
                return;
            }

            var t = $(document).find( '.yith-quick-view .yith-wcqv-wrapper' ),
                window_w = $(window).width(),
                window_h = $(window).height(),
                width    = ( ( window_w - 60 ) > yith_qv.popup_size_width ) ? yith_qv.popup_size_width : ( window_w - 60 ),
                height   = ( ( window_h - 120 ) > yith_qv.popup_size_height ) ? yith_qv.popup_size_height : ( window_h - 120 );

            t.css({
                'left' : (( window_w/2 ) - ( width/2 )),
                'top' : (( window_h/2 ) - ( height/2 )),
                'width'     : width + 'px',
                'height'    : height + 'px'
            });
        };

    if( typeof yith_qv === 'undefined' ) {
        return;
    }

    /*==================
     * INIT PLUGIN
     ==================*/

    $.fn.yith_wcqv_init = function() {

        buttons     = $(document).find( '.yith-wcqv-button' );

        // build products id array
        $.each( buttons, function(){
            var product_id = $(this).data('product_id');
            if( $.inArray( product_id, products_ids ) == -1 ) {
                products_ids.push( product_id );
            }
        } );

        // nav event
        if( qv_nav.length ) {
            nav_ajax_call(qv_nav);
        }
        // close event
        close_modal_qv( qv_close );

        // responsive
        center_modal();

        // off old event ( prevent multiple open )
        buttons.off( 'click' );

        // calculate position
        imagesLoaded( qv_content, function(){
            button_position( buttons );
        });

        $(document).on( 'click', '.yith-wcqv-button', function(e){

            var t           = $(this),
                data_type   = t.data('type'),
                product_id  = t.data( 'product_id' );

            if( ! product_id ) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            if( ! yith_qv.enable_loading ) {
                qv_loader( t );
            }

            // if is inline move modal
            if ( yith_qv.type == 'yith-inline' ) {

                var elem        = t.closest( yith_qv.main_product ),
                    last_elem   = ( elem.hasClass( 'last' ) ) ? elem : elem.nextUntil( '.first', '.last' );

                // make sure to get always last elem
                if( ! last_elem.length || ! last_elem.hasClass('last') ) {
                    last_elem = elem.parent().find( yith_qv.main_product ).last();
                }

                if( elem.closest('.yith-quick-view').length || last_elem.next( '.yith-quick-view' ).length ) {
                    // if in same row of li or is inside the quick view do qv_loader
                    qv_loader( qv_content );
                }
                else if ( qv_modal.hasClass('open') ) {
                    // if in another row close it and move
                    qv_modal.removeClass('open').removeClass('loading');

                    qv_modal.slideUp( 'slow', function(){
                        last_elem.after( qv_modal );

                        // ajax call
                        ajax_call( t, product_id );
                    });

                    return;
                }
                else {
                    // and move it
                    last_elem.after( qv_modal );
                }
            }
            else {
                // add loading effect
                $(document).trigger( 'qv_loading' ); // deprecated
                $(document).trigger( 'yith_quick_view_loading' );

            }

            ajax_call( t, product_id );

        });
    };

    /*=====================
     * MAIN BUTTON POSITION
     =======================*/

    var button_position = function( buttons ){

        if( buttons.length && ! buttons.hasClass( 'inside-thumb' ) ) {
            return;
        }

        $.each( buttons, function () {
            var button  = $(this),
                img     = button.closest( yith_qv.main_product ).find( yith_qv.main_image_class );

            // add position
            button.css({
                'top'     : ( img.height() - button.height() ) / 2 + 'px',
                'right'   : ( img.width() - button.width() ) / 2 + 'px',
                'display' : 'inline-block'
            });
        });
    };


    /*=================
     * LOADER FUNCTION
     ==================*/

    var qv_loader = function(t) {

        if ( typeof yith_qv.loader !== 'undefined'  ) {

            t.block({
                message   : null,
                overlayCSS: {
                    background: '#fff url(' + yith_qv.loader + ') no-repeat center',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });

            $(document).on( 'qv_loader_stop', function(){
                t.unblock();
            });
        }
    };

    /*==============
     * NAVIGATION
     ==============*/

    var nav_ajax_call = function( nav ) {

        var a = nav.find( '> a' );

        // prevent multiple
        a.off( 'click' );

        a.on( 'click', function (e) {
            e.preventDefault();

            var t = $(this),
                product_id = t.data('product_id');

            qv_loader( qv_content );

            ajax_call( t, product_id )
        });
    };


    /*================
     * MAIN AJAX CALL
     ================*/

    var ajax_call = function( t, product_id ) {

        var current_index = $.inArray( product_id, products_ids ),
            prev_id      = products_ids[ current_index - 1 ],
            next_id      = products_ids[ current_index + 1 ],
            // create data for post
            data = {
                action: yith_qv.ajaxQuickView,
                product_id: product_id,
                prev_product_id: prev_id,
                next_product_id: next_id,
                context: 'frontend',
                lang: yith_qv.lang
            };

        if( current_xhr != null ) {
            current_xhr.abort();
        }

        current_xhr = $.ajax({
            url: yith_qv.ajaxurl.toString().replace( '%%endpoint%%', yith_qv.ajaxQuickView ),
            data: data,
            dataType: 'json',
            type: 'GET',
            success: function( data ) {

                qv_content.html( data.html );

                // Init images slider
                if( yith_qv.imagesMode == 'slider' ){
                    imagesLoaded( qv_content, function(){
                        qv_images_slider();
                    });
                }

                //scroll
                if( $(window).width() > 480 ) {
                    qv_content.find('div.summary').perfectScrollbar({
                        suppressScrollX: true
                    });
                }

                if( yith_qv.increment_plugin ) {
                    qv_content.find('div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)').addClass('buttons_added').append('<input type="button" value="+" class="plus" />').prepend('<input type="button" value="-" class="minus" />');
                }

                // Variation Form
                var form_variation = qv_content.find( '.variations_form' );
                form_variation.each( function() {
                    $( this ).wc_variation_form();
                    // add Color and Label Integration
                    if( typeof $.fn.yith_wccl !== 'undefined' ) {
                        $(this).yith_wccl();
                    }
                    else if( typeof $.yith_wccl != 'undefined' && data.prod_attr ) {
                        $.yith_wccl( data.prod_attr );
                    }
                });
                form_variation.trigger( 'check_variations' );
                form_variation.trigger( 'reset_image' );

                // Request a Quote Integration
                if( typeof $.fn.yith_ywraq_variations !== 'undefined' ) {
                    $.fn.yith_ywraq_variations();
                    qv_content.find( '[name|="variation_id"]').trigger('change');
                }

                // One click checkout integration
                var wocc_wrapper = qv_content.find( '.yith-wocc-wrapper' );
                if( wocc_wrapper.length && typeof $.fn.yith_wocc_init != 'undefined' ) {
                    wocc_wrapper.yith_wocc_init();
                }

                // WooCommerce Software Addons
                if( typeof $.fn.init_addon_totals !== 'undefined' ) {
                    qv_content.find( '.cart:not(.cart_group)' ).each( function() {
                        $( this ).init_addon_totals();
                    });
                }else if( typeof woocommerce_addons_params !== 'undefined' ){
                    $( 'body' ).trigger( 'quick-view-displayed' );
                }

                // Init prettyPhoto
                if( typeof $.fn.prettyPhoto !== 'undefined' ) {
                    qv_content.find("a[data-rel^='prettyPhoto']").prettyPhoto({
                        hook: 'data-rel',
                        social_tools: false,
                        theme: 'pp_woocommerce',
                        horizontal_padding: 20,
                        opacity: 0.8,
                        deeplinking: false,
                        show_title: false
                    });
                }

                // add to cart
                if( yith_qv.ajaxcart ) {
                    add_to_cart_ajax( data.product_link, qv_content );
                }

                // change thumb with variation
                var single_variation = form_variation.find( '.single_variation_wrap' );
                change_variation_thumb( single_variation );

                // Contact Form 7 Integration
                if( typeof $.fn.ajaxForm !== 'undefined' && typeof $.fn.wpcf7InitForm !== 'undefined' ) {
                    $.fn.ajaxForm({
                        delegation: true,
                        target    : '#output'
                    });

                    qv_content.find('div.wpcf7 > form').wpcf7InitForm();
                }

                if( qv_nav.length ) {

                    var next = qv_nav.find( '.yith-wcqv-next' ),
                        prev = qv_nav.find( '.yith-wcqv-prev' );

                    if ( data.prev_product ) {
                        //add title and thumb
                        prev.find( 'div' ).html( data.prev_product_preview );
                        //show prev
                        prev.data( 'product_id', data.prev_product ).css({ 'display' : 'block' });
                    }
                    else {
                        prev.css({ 'display' : 'none' });
                    }
                    if ( data.next_product ) {
                        //add title and thumb
                        next.find( 'div' ).html( data.next_product_preview );
                        //show prev
                        next.data( 'product_id', data.next_product ).css({ 'display' : 'block' });
                    }
                    else {
                        next.css({ 'display' : 'none' });
                    }
                }

                if( ! qv_modal.hasClass( 'open' ) ) {
                    qv_modal.removeClass('loading').addClass('open');
                    if( yith_qv.type == 'yith-inline' ) {
                        qv_modal.slideDown('slow');
                    }
                    else {
                        $( 'html' ).addClass( 'yith-quick-view-is-open' );
                    }
                }

                //set current xhr to null
                current_xhr = null;

                // stop loader
                $(document).trigger( 'qv_loader_stop' ); // deprecated
                $(document).trigger( 'yith_quick_view_loaded' );
            }
        });
    };


    /*======================
     * SLIDER IN QUICK VIEW
     =======================*/

    var qv_images_slider = function(){

        var slider_wrapper = qv_content.find( '.yith-quick-view-images-slider');

        if( slider_wrapper.length && typeof $.fn.bxSlider != 'undefined' ) {
            theSlider = slider_wrapper.bxSlider({
                pager: yith_qv.enable_images_slider_pagination,
                prevText: '',
                nextText: ''
            });
        }
    };

    /*=============================
     * CLASSIC STYLE. THUMB CHANGE
     ==============================*/

    $( document ).on('click', '.yith-quick-view-single-thumb', function(){

        $(this).siblings().removeClass('active');

        var main = $(this).parents( '.images' ),
            link = main.find( '.woocommerce-main-image' ),
            big = main.find( 'img.wp-post-image' ),
            attachment = $(this).data('img'),
            attachment_href = $(this).data('href');

        if( ! big.length ){
            big = main.find( 'img.attachment-quick_view_image_size' );
            link = big.closest('a');
        }

        big.attr( 'src', attachment )
            .attr('srcset', attachment)
            .attr('src-orig', attachment);

        link.attr( 'href', attachment_href );
        $(this).addClass('active');

        $(document).trigger('yith_wcqv_change_thumb')
    });

    /*===================
     * CLOSE QUICK VIEW
     ===================*/

    var close_modal_qv = function( close ) {

        // prevent multiple
        close.off('click');

        // close box by click close button
        close.on('click', function (e) {
            e.preventDefault();
            close_qv();
        });


        if ( yith_qv.type != 'yith-inline' ) {
            // close box with esc key
            $(document).keyup(function (e) {
                if (e.keyCode === 27)
                    close_qv();
            });
            // close box by click overlay
            $( '.yith-quick-view-overlay' ).on( 'click', function(e){
                if( ! qv_modal.hasClass('loading') )
                    close_qv();
            });
        }

        var close_qv = function() {
            qv_modal.removeClass('open').removeClass('loading');

            if ( yith_qv.type != 'yith-inline' ) {
                $( 'html' ).removeClass( 'yith-quick-view-is-open' );
                setTimeout(function () {
                    empty_qv();
                }, 1000);
            }
            else {
                qv_modal.slideUp( 'slow', function(){

                    if( yith_qv.closing_cascading == 'scroll-up' ){

                        var offset = $('.yith-quick-view.yith-inline').prev().offset().top;

                        $('html,body').animate({scrollTop: (offset - 100 )  }, 1000);

                    }
                    empty_qv();
                });
            }
        };

        var empty_qv = function(){
            qv_content.html('');
            $(document).trigger('qv_is_closed');
        }
    };

    /*===============================================
     * INFINITE SCROLLING AND AJAX NAV COMPATIBILITY
     ================================================*/

    $( document ).on( 'yith_infs_adding_elem yith-wcan-ajax-filtered', function(){
        // RESTART
        $.fn.yith_wcqv_init();
    });

    /*===================
     * ADD TO CART IN AJAX
     ====================*/

    var add_to_cart_ajax = function( product_url, cont ) {
        cont.find( 'form.cart' ).on('submit', function (e) {

            var form    = $(this),
                button  = form.find( 'button' ),
                is_one_click = form.find('input[name="_yith_wocc_one_click"]').val() == 'is_one_click',
                data;

            if( is_one_click ) {
                return;
            }

            e.preventDefault();

            // Process Form
            var dataForm = new FormData();
            $.each( form.find( "input[type='file']" ), function( i, tag ) {
                $.each( $(tag)[0].files, function( i, file ) {
                    dataForm.append( tag.name, file );
                });
            });


            var has_add_to_cart = false;
            data = form.serializeArray();

            $.each( data, function( i, val ) {
                if( val.name == 'add-to-cart' ) {
                    has_add_to_cart = true;
                }
                dataForm.append( val.name, val.value );
            });
            dataForm.append( 'wp_http_referer', product_url );
            if( ! has_add_to_cart ) {
                dataForm.append('add-to-cart', button.val());
            }

            dataForm.append('context', 'frontend');
            dataForm.append( 'action', yith_qv.ajaxAddToCart );

            if( typeof yith_qv.loader !== 'undefined' ){
                button.block({
                    message   : null,
                    overlayCSS: {
                        background: '#fff url(' + yith_qv.loader + ') no-repeat center',
                        opacity   : 0.5,
                        cursor    : 'none'
                    }
                });
            }

            $.ajax({
                url: product_url,
                data: dataForm,
                contentType: false,
                processData: false,
                type:  'POST',
                dataType: 'json',
                success: function (result) {

                    if( yith_qv.redirect_checkout ){
                        window.location.href = yith_qv.checkout_url;
                        return;
                    }

                    var message         = typeof result.fragments.ywcqv_messages != 'undefined' ? result.fragments.ywcqv_messages : '', // get standard message
                        summary         = cont.find( 'div.summary');

                    summary.find( '.woocommerce-message, .woocommerce-error').remove();
                    if( message.length ){
                        summary.prepend( message );
                    }

                    if( typeof yith_qv.loader !== 'undefined' ){
                        button.unblock();
                    }

                    // then scroll to Top
                    if( yith_qv.ismobile ) {
                        qv_content.scrollTop( summary.position().top );
                    } else {
                        summary.scrollTop(0);
                    }

                    // Trigger event so themes can refresh other areas.
                    $( document.body ).trigger( 'added_to_cart', [ result.fragments, result.cart_hash, button ] );

                    if( yith_qv.closeOnAjaxCart && typeof result.fragments.ywcqv_error == 'undefined' ){
                        setTimeout( function () {
                            qv_close.click();
                        }, yith_qv.timeout_close_quick_view );
                    }
                } });
        });
    };

    /*********************************
     * ON VARIATION SELECT CHANGE THUMB
     * @param variation
     ***********************************/

    var change_variation_thumb = function( variation ){

        var slider          = [],
            change_thumb    = function( variation_data ) {
                // classic thumb
                var images          = qv_content.find( 'div.images' ),
                    thumbs          = images.find( '.yith-quick-view-thumbs' ),
                    product_img     = images.find( '.attachment-quick_view_image_size' ).first(),
                    product_link    = images.find( 'a.woocommerce-main-image' ),
                    classic = thumbs.find( '.yith-quick-view-single-thumb[data-attachment_id="' + variation_data.attachment_id + '"]' ),
                    slide   = $.inArray( variation_data.attachment_id, slider );

                if( classic.length ) {
                    classic.click();
                }
                else if( slide !== -1 && theSlider !== null ) {
                    theSlider.goToSlide( slide );
                }
                else if ( variation_data.image && variation_data.image.src && variation_data.image.src.length > 1 ) {
                    product_img.wc_set_variation_attr( 'src', variation_data.image.src );
                    product_img.wc_set_variation_attr( 'height', variation_data.image.src_h );
                    product_img.wc_set_variation_attr( 'width', variation_data.image.src_w );
                    product_img.wc_set_variation_attr( 'srcset', variation_data.image.srcset );
                    product_img.wc_set_variation_attr( 'sizes', variation_data.image.sizes );
                    product_img.wc_set_variation_attr( 'title', variation_data.image.title );
                    product_img.wc_set_variation_attr( 'alt', variation_data.image.alt );
                    product_img.wc_set_variation_attr( 'data-src', variation_data.image.full_src );
                    product_img.wc_set_variation_attr( 'data-large_image', variation_data.image.full_src );
                    product_img.wc_set_variation_attr( 'data-large_image_width', variation_data.image.full_src_w );
                    product_img.wc_set_variation_attr( 'data-large_image_height', variation_data.image.full_src_h );
                    product_link.wc_set_variation_attr( 'href', variation_data.image.full_src );

                    thumbs.find( '.yith-quick-view-single-thumb' ).removeClass('active');
                }
            },
            reset_thumb     = function() {

                var images          = qv_content.find( 'div.images' ),
                    thumbs          = images.find( '.yith-quick-view-thumbs' ),
                    product_img     = images.find( '.attachment-quick_view_image_size' ).first(),
                    product_link    = images.find( 'a.woocommerce-main-image' );

                product_img.wc_reset_variation_attr( 'src' );
                product_img.wc_reset_variation_attr( 'width' );
                product_img.wc_reset_variation_attr( 'height' );
                product_img.wc_reset_variation_attr( 'srcset' );
                product_img.wc_reset_variation_attr( 'sizes' );
                product_img.wc_reset_variation_attr( 'title' );
                product_img.wc_reset_variation_attr( 'alt' );
                product_img.wc_reset_variation_attr( 'data-src' );
                product_img.wc_reset_variation_attr( 'data-large_image' );
                product_img.wc_reset_variation_attr( 'data-large_image_width' );
                product_img.wc_reset_variation_attr( 'data-large_image_height' );
                product_link.wc_reset_variation_attr( 'href' );

                thumbs.find( '.yith-quick-view-single-thumb' ).first().click();
                if( theSlider !== null ) {
                    theSlider.goToSlide(0)
                }
            },
            load_gallery    = function( variation_data ){

                if( ! yith_qv.ajaxVariationGallery || yith_qv.imagesMode == 'none' || ( ! variation_data.has_custom_gallery && ! qv_content.find( 'div.images' ).hasClass( 'has_custom_gallery' ) ) ) {
                    return false;
                }

                if( current_xhr != null ) {
                    current_xhr.abort();
                }

                current_xhr = $.ajax({
                    url: yith_qv.ajaxurl.toString().replace( '%%endpoint%%', yith_qv.ajaxVariationGallery ),
                    data: {
                        action: yith_qv.ajaxVariationGallery,
                        id     : variation_data ? variation_data.variation_id : $('input#yith_wcqv_product_id').val(),
                        context: 'frontend'
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function(){
                        qv_content.find( 'div.images' ).addClass( 'loading-gallery' );
                    },
                    success: function( response ){
                        qv_content.find( 'div.images' ).replaceWith( response.html );

                        if( yith_qv.imagesMode == 'slider' ) {
                            slider = [];
                            qv_images_slider(); // init slider
                        }

                        if( variation_data ) { // if isset variation data try to change thumb
                            change_thumb( variation_data );
                        }

                        // Init prettyPhoto
                        if( typeof $.fn.prettyPhoto !== 'undefined' ) { // init prettyPhoto
                            qv_content.find("a[data-rel^='prettyPhoto']").prettyPhoto({
                                hook: 'data-rel',
                                social_tools: false,
                                theme: 'pp_woocommerce',
                                horizontal_padding: 20,
                                opacity: 0.8,
                                deeplinking: false,
                                show_title: false
                            });
                        }

                        current_xhr = null;
                        if( ! response.default ) { // if not default add custom class
                            qv_content.find( 'div.images' ).addClass( 'has_custom_gallery' );
                        }
                        $(document).trigger( 'qv_variation_gallery_loaded' );
                    }
                });

                return true;
            };

        $('.yith-quick-view-images-slider').find('.yith-quick-view-slide').each( function( i, v ){
            slider[i] = $(this).data('attachment_id') + '';
        });

        // show event
        variation.on( 'show_variation', function( ev, data ){

            if( typeof data.attachment_id == 'undefined' || load_gallery( data ) ) {
                return;
            }

            change_thumb( data );
        });
        // hide event
        variation.on( 'hide_variation', function(){

            if( load_gallery( false ) ) {
                return;
            }

            reset_thumb();
        });
    };

    /*************************************
     * ADD LOADING OVERLAY
     ************************************/

    $(document).on( 'qv_loading', function(){

        if( ! yith_qv.enable_loading ) {
            return false;
        }

        var qv_modal    = $(document).find( '.yith-quick-view' ),
            qv_overlay  = qv_modal.find( '.yith-quick-view-overlay');

        if( ! qv_modal.hasClass( 'loading' ) ) {
            qv_modal.addClass('loading');
        }

        if ( ! qv_overlay.find('p').length ) {
            var p = $('<p />').text( yith_qv.loading_text );
            qv_overlay.append( p );
        }
    });

    /***************************************
     * ZOOM MAGNIFIER
     **************************************/

    if( yith_qv.enable_zoom && typeof $.fn.yith_magnifier != 'undefined' ) {

        $(document).on('qv_loader_stop qv_variation_gallery_loaded', function (ev) {

            if( typeof yith_magnifier_options == 'undefined' ){
                return false;
            }

            var yith_wcmg               = $('.yith-quick-view-content .images' ),
                yith_wcmg_zoom          = yith_wcmg.find('.yith_magnifier_zoom' ),
                yith_wcmg_image         = yith_wcmg.find('.yith_magnifier_zoom img.attachment-quick_view_image_size' ),
                yith_wcmg_default_zoom  = yith_wcmg.find('.yith_magnifier_zoom').attr('href'),
                yith_wcmg_default_image = yith_wcmg.find('.yith_magnifier_zoom img').attr('src');

            // first destroy previous instance if any
            if (yith_wcmg.data('yith_magnifier')) {
                yith_wcmg.yith_magnifier('destroy');
            }

            yith_wcmg_zoom.attr('href', yith_wcmg_default_zoom);
            yith_wcmg_image.attr('src', yith_wcmg_default_image);
            yith_wcmg_image.attr('srcset', yith_wcmg_default_image);
            yith_wcmg_image.attr('src-orig', yith_wcmg_default_image);

            var $opts = {
                enableSlider: false,
                position: 'inside',
                elements: {
                    zoom: yith_wcmg_zoom,
                    zoomImage: yith_wcmg_image
                }
            };

            $opts = $.extend(true, {}, yith_magnifier_options, $opts );

            // remove expand prettyPhoto
            yith_wcmg.find( 'a.pp_expand, a.yith_expand' ).remove();
            // finally init zoom
            yith_wcmg.yith_magnifier($opts);

            $(document).off('yith_wcqv_change_thumb').on('yith_wcqv_change_thumb', function () {
                if (yith_wcmg.data('yith_magnifier')) {
                    yith_wcmg.yith_magnifier('destroy');
                }
                yith_wcmg.yith_magnifier($opts);
            });

            $(document).on('found_variation', 'form.variations_form', function (event, variation) {

                var image_src = false;
                if( variation.image_src && variation.image_src.length ) {
                    image_src = variation.image_src;
                }
                else if( variation.image.src ) {
                    image_src = variation.image.src;
                }

                if( ! image_src ) {
                    return false;
                }

                yith_wcmg_zoom.attr('href', image_src);
                yith_wcmg_image.attr('src', image_src);
                yith_wcmg_image.attr('srcset', image_src);
                yith_wcmg_image.attr('src-orig', image_src);

                if (yith_wcmg.data('yith_magnifier')) {
                    yith_wcmg.yith_magnifier('destroy');
                }
                yith_wcmg.yith_magnifier($opts);

            }).on('reset_image', function (event) {

                yith_wcmg_zoom.attr('href', yith_wcmg_default_zoom);
                yith_wcmg_image.attr('src', yith_wcmg_default_image);
                yith_wcmg_image.attr('srcset', yith_wcmg_default_image);
                yith_wcmg_image.attr('src-orig', yith_wcmg_default_image);

                if (yith_wcmg.data('yith_magnifier')) {
                    yith_wcmg.yith_magnifier('destroy');
                }
                yith_wcmg.yith_magnifier($opts);
            });
        });
    }

    $(document).on('qv_loader_stop', function () {
        if ( yith_qv.type == 'yith-inline' ) {
            $('html, body').animate({
                scrollTop: $(".yith-quick-view").offset().top - 100
            }, 1000);
        }
    });

    /********************
     * DOUBLE TAP MOBILE
     ********************/
    $.fn.YitdoubleTapToGo = function () {
        this.each(function () {
            var t = false,
                p = $(this).closest('.product');

            $(document).on('qv_loader_stop', function(){
                p.removeClass('hover_mobile');
            });

            $(this).on( "touchstart", function (e) {
                // RESET ALL
                p.siblings().removeClass('hover_mobile');

                if( ! t || ! p.hasClass( 'hover_mobile' ) ) {
                    e.preventDefault();
                    p.addClass('hover_mobile');
                    t = true;
                }
            });
        });

        return this
    };
    // Double tap init
    if( yith_qv.ismobile && yith_qv.enable_double_tab ) {
        $( yith_qv.main_product_link ).YitdoubleTapToGo();
    }

    // START
    $.fn.yith_wcqv_init();

    // re init on WC Ajax Filters Update
    $(document).on('yith_wcqv_wcajaxnav_update post-load', $.fn.yith_wcqv_init );

    // caluclate positions on windows resize
    $( window ).on( 'resize', function(){
        center_modal();
        button_position( $(document).find( '.yith-wcqv-button' ) );
    });

    $(document).on( 'facetwp-loaded',function(){
        button_position( $(document).find( '.yith-wcqv-button' ) );
    } );

    // compatibility with Variation Swatches for Woocommerce
    $(document).on('qv_loader_stop', function(){
        if( typeof $.fn.tawcvs_variation_swatches_form != 'undefined' ) {
            $('.variations_form').tawcvs_variation_swatches_form();
            $(document.body).trigger('tawcvs_initialized');
        }
    });
});