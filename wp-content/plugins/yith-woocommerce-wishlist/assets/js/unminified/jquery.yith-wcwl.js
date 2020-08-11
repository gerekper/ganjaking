/**
 * Main YITH WooCommerce Wishlist JS
 *
 * @author YITH
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

jQuery( document ).ready( function( $ ){

    /* === MAIN INIT === */

    $(document).on( 'yith_wcwl_init', function(){
        var t = $(this),
            cart_redirect_after_add = ( typeof( wc_add_to_cart_params ) !== 'undefined' && wc_add_to_cart_params !== null ) ? wc_add_to_cart_params.cart_redirect_after_add : '';

        t.on( 'click', '.add_to_wishlist', function( ev ) {
            var t = $(this),
                product_id = t.attr( 'data-product-id' ),
                el_wrap = $( '.add-to-wishlist-' + product_id ),
                filtered_data = null,
                data = {
                    add_to_wishlist: product_id,
                    product_type: t.data( 'product-type' ),
                    wishlist_id: t.data( 'wishlist-id' ),
                    action: yith_wcwl_l10n.actions.add_to_wishlist_action,
                    fragments: retrieve_fragments( product_id )
                };

            // allow third party code to filter data
            if( filtered_data = $(document).triggerHandler( 'yith_wcwl_add_to_wishlist_data', [ t, data ] ) ) {
                data = filtered_data;
            }

            ev.preventDefault();

            jQuery( document.body ).trigger( 'adding_to_wishlist' );

            if( yith_wcwl_l10n.multi_wishlist && yith_wcwl_l10n.modal_enable ){
                var wishlist_popup_container = t.parents( '.yith-wcwl-popup-footer' ).prev( '.yith-wcwl-popup-content' ),
                    wishlist_popup_select = wishlist_popup_container.find( '.wishlist-select' ),
                    wishlist_popup_name = wishlist_popup_container.find( '.wishlist-name' ),
                    wishlist_popup_visibility = wishlist_popup_container.find( '.wishlist-visibility' ).filter(':checked');

                data.wishlist_id = wishlist_popup_select.is(':visible') ? wishlist_popup_select.val() : 'new';
                data.wishlist_name = wishlist_popup_name.val();
                data.wishlist_visibility = wishlist_popup_visibility.val();

                if( 'new' === data.wishlist_id && ! data.wishlist_name ){
                    wishlist_popup_name.closest('p').addClass('woocommerce-invalid');
                    return false;
                }
                else{
                    wishlist_popup_name.closest('p').removeClass('woocommerce-invalid');
                }
            }

            if( ! is_cookie_enabled() ){
                alert( yith_wcwl_l10n.labels.cookie_disabled );
                return;
            }

            $.ajax({
                type: 'POST',
                url: yith_wcwl_l10n.ajax_url,
                data: data,
                dataType: 'json',
                beforeSend: function(){
                    block( t );
                },
                complete: function(){
                    unblock( t );
                },
                success: function( response ) {
                    var response_result = response.result,
                        response_message = response.message;

                    if( yith_wcwl_l10n.multi_wishlist ) {
                        // close PrettyPhoto popup
                        close_pretty_photo( response_message );

                        // update options for all wishlist selects
                        if( typeof( response.user_wishlists ) != 'undefined' ) {
                            update_wishlists( response.user_wishlists );
                        }
                    }
                    else {
                        print_message(response_message);
                    }

                    if( response_result === "true" || response_result === "exists" ) {
                        if( typeof response.fragments != 'undefined' ) {
                            replace_fragments( response.fragments );
                        }

                        if( ! yith_wcwl_l10n.multi_wishlist || yith_wcwl_l10n.hide_add_button ) {
                            el_wrap.find('.yith-wcwl-add-button').remove();
                        }

                        el_wrap.addClass('exists');
                    }

                    init_handling_after_ajax();

                    $('body').trigger('added_to_wishlist', [ t, el_wrap ] );
                }

            });

            return false;
        } );

        t.on( 'click', '.wishlist_table .remove_from_wishlist', function( ev ){
            var t = $( this );

            ev.preventDefault();

            remove_item_from_wishlist( t );

            return false;
        } );

        t.on( 'adding_to_cart', 'body', function( ev, button, data ){
            if( typeof button != 'undefined' && typeof data != 'undefined' && button.closest( '.wishlist_table' ).length ){
                data.remove_from_wishlist_after_add_to_cart = button.closest( '[data-row-id]' ).data( 'row-id' );
                data.wishlist_id = button.closest( '.wishlist_table' ).data( 'id' );
                typeof wc_add_to_cart_params !== 'undefined' && ( wc_add_to_cart_params.cart_redirect_after_add = yith_wcwl_l10n.redirect_to_cart );
                typeof yith_wccl_general !== 'undefined' && ( yith_wccl_general.cart_redirect = yith_wcwl_l10n.redirect_to_cart );
            }
        } );

        t.on( 'added_to_cart', 'body', function( ev, fragments, carthash, button ){
            if( typeof button != 'undefined' && button.closest( '.wishlist_table' ).length ) {
                typeof wc_add_to_cart_params !== 'undefined' && ( wc_add_to_cart_params.cart_redirect_after_add = cart_redirect_after_add );
                typeof yith_wccl_general !== 'undefined' && ( yith_wccl_general.cart_redirect = cart_redirect_after_add );

                var tr = button.closest('[data-row-id]'),
                    table = tr.closest('.wishlist-fragment'),
                    options = table.data('fragment-options');

                button.removeClass('added');
                tr.find('.added_to_cart').remove();

                if ( yith_wcwl_l10n.remove_from_wishlist_after_add_to_cart && options.is_user_owner ) {
                    tr.remove();
                }
            }
        } );

        t.on( 'added_to_cart', 'body', function(){
            var messages = $( '.woocommerce-message');

            if( messages.length === 0 ){
                $( '#yith-wcwl-form').prepend( yith_wcwl_l10n.labels.added_to_cart_message );
            }
            else{
                messages.fadeOut( 300, function(){
                    $(this).replaceWith( yith_wcwl_l10n.labels.added_to_cart_message ).fadeIn();
                } );
            }
        } );

        t.on( 'cart_page_refreshed', 'body', init_handling_after_ajax );

        t.on( 'click', '.show-title-form', show_title_form );

        t.on( 'click', '.wishlist-title-with-form h2', show_title_form );

        t.on( 'click', '.remove_from_all_wishlists', function( ev ){
            var t = $(this),
                prod_id = t.attr('data-product-id'),
                wishlist_id = t.data('wishlist-id'),
                content = t.closest( '.content' ),
                data = {
                    action: yith_wcwl_l10n.actions.remove_from_all_wishlists,
                    prod_id: prod_id,
                    wishlist_id: wishlist_id,
                    fragments: retrieve_fragments( prod_id )
                };

            ev.preventDefault();

            $.ajax({
                beforeSend: function(){
                    block( content );
                },
                complete: function(){
                    unblock( content );
                },
                data: data,
                dataType: 'json',
                method: 'post',
                success: function( data ){
                    if( typeof data.fragments != 'undefined' ){
                        replace_fragments( data.fragments );
                    }

                    init_handling_after_ajax();
                },
                url: yith_wcwl_l10n.ajax_url
            });
        } );

        t.on( 'click', '.hide-title-form', hide_title_form );

        t.on( 'click', '.save-title-form', submit_title_form );

        t.on( 'change', '.wishlist_manage_table .wishlist-visibility', save_privacy );

        t.on( 'change', '.change-wishlist', function(){
            var t = $(this),
                table = t.parents( '.cart.wishlist_table'),
                wishlist_token = table.data('token'),
                item_id = t.parents( '[data-row-id]').data('row-id'),
                to_token = t.val();

            call_ajax_move_item_to_another_wishlist(
                {
                    wishlist_token            : wishlist_token,
                    destination_wishlist_token: to_token,
                    item_id                   : item_id,
                    fragments                 : retrieve_fragments()
                },
                function(){
                    block( table );
                },
                function( data ){
                    if( typeof data.fragments != 'undefined' ) {
                        replace_fragments( data.fragments );
                    }

                    unblock( table );
                }
            );
        } );

        t.on( 'click', '.yith-wcwl-popup-footer .move_to_wishlist', function(){
            var t = $(this),
                product_id = t.attr('data-product-id'),
                wishlist_token = t.data('origin-wishlist-id'),
                form = t.closest('form'),
                to_token = form.find('.wishlist-select').val(),
                wishlist_name_field = form.find('.wishlist-name'),
                wishlist_name = wishlist_name_field.val(),
                wishlist_visibility = form.find('.wishlist-visibility').filter(':checked').val();

            if( 'new' === to_token && ! wishlist_name ){
                wishlist_name_field.closest('p').addClass('woocommerce-invalid');
                return false;
            }
            else{
                wishlist_name_field.closest('p').removeClass('woocommerce-invalid');
            }

            call_ajax_move_item_to_another_wishlist(
                {
                    wishlist_token            : wishlist_token,
                    destination_wishlist_token: to_token,
                    item_id                   : product_id,
                    wishlist_name             : wishlist_name,
                    wishlist_visibility       : wishlist_visibility,
                    fragments                 : retrieve_fragments( product_id )
                },
                function(){
                    block( t );
                },
                function( response ){
                    var response_message = response.message;

                    if( yith_wcwl_l10n.multi_wishlist ) {
                        close_pretty_photo( response_message );

                        if( typeof( response.user_wishlists ) != 'undefined' ){
                            update_wishlists( response.user_wishlists );
                        }
                    }
                    else {
                        print_message(response_message);
                    }

                    if( typeof response.fragments != 'undefined' ) {
                        replace_fragments( response.fragments );
                    }

                    init_handling_after_ajax();

                    unblock( t );
                }
            );
        } );

        t.on( 'click', '.delete_item', function(){
            var t = $(this),
                product_id = t.attr('data-product-id'),
                item_id = t.data('item-id'),
                el_wrap = $( '.add-to-wishlist-' + product_id );

            $.ajax( {
                url: yith_wcwl_l10n.ajax_url,
                data : {
                    action: yith_wcwl_l10n.actions.delete_item_action,
                    item_id: item_id,
                    fragments: retrieve_fragments( product_id )
                },
                dataType: 'json',
                beforeSend: function(){
                    block( t )
                },
                complete: function(){
                    unblock( t );
                },
                method: 'post',
                success: function( response ){
                    var fragments = response.fragments,
                        response_message = response.message;

                    if( yith_wcwl_l10n.multi_wishlist ) {
                        close_pretty_photo( response_message );
                    }

                    if( ! t.closest( '.yith-wcwl-remove-button' ).length ){
                        print_message(response_message);
                    }

                    if( typeof fragments != 'undefined' ){
                        replace_fragments( fragments );
                    }

                    init_handling_after_ajax();

                    $('body').trigger('removed_from_wishlist', [ t, el_wrap ] );
                }
            } );

            return false;
        } );

        t.on( 'change', '.yith-wcwl-popup-content .wishlist-select', function(){
            var t = $(this);

            if( t.val() === 'new' ){
                t.parents( '.yith-wcwl-first-row' ).next( '.yith-wcwl-second-row' ).show();
            }
            else{
                t.parents( '.yith-wcwl-first-row' ).next( '.yith-wcwl-second-row' ).hide();
            }
        } );

        t.on( 'change', '#bulk_add_to_cart', function(){
            var t = $(this),
                checkboxes = t.closest( '.wishlist_table' ).find( '[data-row-id]' ).find( 'input[type="checkbox"]:not(:disabled)' );

            if( t.is( ':checked' ) ){
                checkboxes.attr( 'checked','checked').change();
            }
            else{
                checkboxes.removeAttr( 'checked').change();
            }
        } );

        t.on( 'submit', '.wishlist-ask-an-estimate-popup', function(){
            var t = $(this),
                form = t.closest( 'form' ),
                pp_content = t.closest('.pp_content'),
                data = form.serialize();

            $.ajax({
                beforeSend: function(){
                    block( form );
                },
                complete: function(){
                    unblock( form );
                },
                data: data + '&action=' + yith_wcwl_l10n.actions.ask_an_estimate,
                dataType: 'json',
                method: 'post',
                success: function( data ){
                    if( typeof data.result != 'undefined' && data.result ){
                        var template = data.template;

                        if( typeof template != 'undefined' ){
                            form.replaceWith( template );
                            pp_content.css('height', 'auto');

                            setTimeout( close_pretty_photo, 3000 );
                        }
                    }
                    else if( typeof data.message != 'undefined' ){
                        form.find( '.woocommerce-error' ).remove();
                        form.find( '.popup-description' ).after( $('<div>', {
                            text: data.message,
                            class: 'woocommerce-error'
                        } ) )
                    }
                },
                url: yith_wcwl_l10n.ajax_url
            });

            return false;
        } );

        t.on( 'click', '.yith-wfbt-add-wishlist', function(e){
            e.preventDefault();
            var t    = $(this),
                form = $( '#yith-wcwl-form' );

            $('html, body').animate({
                scrollTop: ( form.offset().top)
            },500);

            // ajax call
            reload_wishlist_and_adding_elem( t, form );
        });

        t.on( 'submit', '.yith-wcwl-popup-form', function(){
            return false;
        } );

        t.on( 'yith_infs_added_elem', function(){
            init_wishlist_pretty_photo();
        } );

        t.on( 'found_variation', function( ev, variation ){
            var t = $( ev.target ),
                product_id = t.data( 'product_id' ),
                variation_id = variation.variation_id,
                targets = $('.add_to_wishlist[data-product-id="' + product_id + '"]').add('.add_to_wishlist[data-original-product-id="' + product_id + '"]');

            if( ! product_id || ! variation_id || ! targets.length ){
                return;
            }

            targets.each( function(){
                var t = $(this),
                    container = t.closest( '.yith-wcwl-add-to-wishlist' ),
                    options;

                t.attr( 'data-original-product-id', product_id );
                t.attr( 'data-product-id', variation_id );

                if( container.length ) {
                    options = container.data( 'fragment-options' );

                    if( typeof options != 'undefined' ){
                        options.product_id = variation_id;
                        container.data( 'fragment-options', options );
                    }

                    container
                        .removeClass(function (i, classes) {
                            return classes.match(/add-to-wishlist-\S+/g).join(' ');
                        })
                        .addClass('add-to-wishlist-' + variation_id)
                        .attr('data-fragment-ref', variation_id);
                }
            } );
        } );

        t.on( 'reset_data', function( ev ){
            var t = $( ev.target ),
                product_id = t.data( 'product_id' ),
                targets = $('.add_to_wishlist[data-original-product-id="' + product_id + '"]');

            if( ! product_id || ! targets.length ){
                return;
            }

            targets.each( function(){
                var t = $(this),
                    container = t.closest( '.yith-wcwl-add-to-wishlist' ),
                    variation_id = t.attr( 'data-product-id' ),
                    options;

                t.attr( 'data-product-id', product_id );
                t.attr( 'data-original-product-id', '' );

                if( container.length ) {
                    options = container.data( 'fragment-options' );

                    if( typeof options != 'undefined' ){
                        options.product_id = product_id;
                        container.data( 'fragment-options', options );
                    }

                    container
                        .removeClass('add-to-wishlist-' + variation_id)
                        .addClass('add-to-wishlist-' + product_id)
                        .attr('data-fragment-ref', product_id);
                }
            } );
        } );

        t.on( 'yith_wcwl_reload_fragments', load_fragments );

        t.on( 'yith_infs_added_elem', function( ev, elem ){
            load_fragments( {
                container: elem,
                firstLoad: false
            } );
        } );

        t.on( 'yith_wcwl_fragments_loaded', function( ev ){
            $( '.variations_form' ).find( '.variations select' ).last().change();
        } );

        t.on( 'click', '.yith-wcwl-popup-feedback .close-popup', function(){
            close_pretty_photo();
        } )

        init_wishlist_popup();

        init_wishlist_tooltip();

        init_wishlist_dropdown();

        init_wishlist_drag_n_drop();

        init_quantity();

        init_wishlist_details_popup();

        init_wishlist_popup_tabs();

        init_select_box();

        init_checkbox_handling();

        init_wishlist_pretty_photo();

        init_add_to_cart_icon();

        init_wishlist_responsive();

        init_copy_wishlist_link();

        load_fragments();

    } ).trigger('yith_wcwl_init');

    /* === INIT FUNCTIONS === */

    /**
     * Adds selectbox where needed
     *
     * @return void
     * @since 3.0.0
     */
    function init_select_box() {
        if( typeof $.fn.selectBox != 'undefined' ) {
            $('select.selectBox').filter(':visible').not('.enhanced').selectBox().addClass('enhanced');
        }
    }

    /**
     * Init PrettyPhoto for all links withi the plugin that open a popup
     *
     * @return void
     * @since 2.0.16
     */
    function init_wishlist_pretty_photo() {
        if( typeof $.prettyPhoto == 'undefined' ){
            return;
        }

        var ppParams = {
            hook                  : 'data-rel',
            social_tools          : false,
            theme                 : 'pp_woocommerce',
            horizontal_padding    : 20,
            opacity               : 0.8,
            deeplinking           : false,
            overlay_gallery       : false,
            default_width         : 500,
            changepicturecallback : function(){
                init_select_box();

                $('.wishlist-select').change();
                $(document).trigger( 'yith_wcwl_popup_opened', [ this ] );
            },
            markup: '<div class="pp_pic_holder">' +
                '<div class="ppt">&nbsp;</div>' +
                '<div class="pp_top">' +
                '<div class="pp_left"></div>' +
                '<div class="pp_middle"></div>' +
                '<div class="pp_right"></div>' +
                '</div>' +
                '<div class="pp_content_container">' +
                '<div class="pp_left">' +
                '<div class="pp_right">' +
                '<div class="pp_content">' +
                '<div class="pp_loaderIcon"></div>' +
                '<div class="pp_fade">' +
                '<a href="#" class="pp_expand" title="Expand the image">Expand</a>' +
                '<div class="pp_hoverContainer">' +
                '<a class="pp_next" href="#">next</a>' +
                '<a class="pp_previous" href="#">previous</a>' +
                '</div>' +
                '<div id="pp_full_res"></div>' +
                '<div class="pp_details">' +
                '<a class="pp_close" href="#">Close</a>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="pp_bottom">' +
                '<div class="pp_left"></div>' +
                '<div class="pp_middle"></div>' +
                '<div class="pp_right"></div>' +
                '</div>' +
                '</div>' +
                '<div class="pp_overlay yith-wcwl-overlay"></div>'
        };

        $('a[data-rel^="prettyPhoto[add_to_wishlist_"]')
            .add('a[data-rel="prettyPhoto[ask_an_estimate]"]')
            .add('a[data-rel="prettyPhoto[create_wishlist]"]')
            .unbind( 'click' )
            .prettyPhoto( ppParams );

        $('a[data-rel="prettyPhoto[move_to_another_wishlist]"]').on( 'click', function(){
            var t = $(this),
                popup = $('#move_to_another_wishlist'),
                form = popup.find('form'),
                row_id = form.find( '.row-id' ),
                id = t.closest('[data-row-id]').data('row-id');

            if( row_id.length ){
                row_id.remove();
            }

            form.append( '<input type="hidden" name="row_id" class="row-id" value="' + id + '"/>' );
        } ).prettyPhoto( ppParams );

        // add & remove class to body when popup is opened
        var observer = new MutationObserver( function( mutationsList, observer ){
            for ( var i in mutationsList ) {
                var mutation = mutationsList[ i ];
                if ( mutation.type === 'childList' ) {
                  typeof mutation.addedNodes !== 'undefined' && mutation.addedNodes.forEach( function( currentValue ){
                      if( typeof currentValue.classList !== 'undefined' && currentValue.classList.contains( 'yith-wcwl-overlay' ) ){
                          $('body').addClass( 'yith-wcwl-with-pretty-photo' );
                      }
                  } );

                  typeof mutation.removedNodes !== 'undefined' && mutation.removedNodes.forEach( function( currentValue ){
                      if( typeof currentValue.classList !== 'undefined' && currentValue.classList.contains( 'yith-wcwl-overlay' ) ){
                          $('body').removeClass( 'yith-wcwl-with-pretty-photo' );
                      }
                  } );
                }
            }
        } );

        observer.observe( document.body, {
          childList: true
        } );
    }

    /**
     * Init checkbox handling
     *
     * @return void
     * @since 3.0.0
     */
    function init_checkbox_handling() {
        var checkboxes = $('.wishlist_table').find('.product-checkbox input[type="checkbox"]');

        checkboxes.off('change').on( 'change', function(){
            var t = $(this),
                p = t.parent();

            p
                .removeClass( 'checked' )
                .removeClass( 'unchecked' )
                .addClass( t.is(':checked') ? 'checked' : 'unchecked' );
        } ).trigger('change');
    }

    /**
     * Show icon on Add to Cart button
     *
     * @return void
     */
    function init_add_to_cart_icon() {
        $('.add_to_cart').filter('[data-icon]').not('.icon-added').each( function(){
            var t = $(this),
                data = t.data('icon'),
                icon;

            if( data.match( /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?/gi ) ){
                icon = $( '<img/>', { src: data } );
            }
            else{
                icon = $( '<i/>', { class: 'fa ' + data } );
            }

            t.prepend( icon ).addClass('icon-added');
        } )
    }

    /**
     * Init js handling on wishlist table items after ajax update
     *
     * @return void
     * @since 2.0.7
     */
    function init_handling_after_ajax() {
        init_select_box();
        init_wishlist_pretty_photo();
        init_checkbox_handling();
        init_add_to_cart_icon();
        init_wishlist_dropdown();
        init_wishlist_tooltip();
        init_wishlist_details_popup();
        init_wishlist_drag_n_drop();
        init_copy_wishlist_link();

        $(document).trigger( 'yith_wcwl_init_after_ajax' );
    }

    /**
     * Add tooltip to Add to Wishlist buttons
     *
     * @return void
     * @since 3.0.0
     */
    function init_wishlist_tooltip() {
        if( ! yith_wcwl_l10n.enable_tooltip ){
            return;
        }

        $('.yith-wcwl-add-to-wishlist').find('[data-title]').each( function(){
            var t = $(this);

            if( t.hasClass( 'tooltip-added' ) ){
                return;
            }

            t
                .on('mouseenter', function () {
                    var t = $(this),
                        tooltip = null,
                        wrapperWidth = t.outerWidth(),
                        left = 0,
                        width = 0;

                    tooltip = $('<span>', {class: 'yith-wcwl-tooltip', text: t.data('title')});

                    t.append(tooltip);

                    width = tooltip.outerWidth() + 6;
                    tooltip.outerWidth(width);
                    left = (wrapperWidth - width) / 2;

                    tooltip.css({left: left.toFixed(0) + 'px'}).fadeIn(200);

                    t.addClass('with-tooltip');
                })
                .on('mouseleave', function () {
                    var t = $(this);

                    t.find('.yith-wcwl-tooltip').fadeOut(200, function(){
                        t.removeClass('with-tooltip').find('.yith-wcwl-tooltip').remove();
                    });
                });

            t.addClass('tooltip-added');
        } );
    }

    /**
     * Add wishlist popup message
     *
     * @return void
     * @since 2.0.0
     */
    function init_wishlist_popup() {
        if( typeof yith_wcwl_l10n.enable_notices != 'undefined' && ! yith_wcwl_l10n.enable_notices ){
            return;
        }

        if( $('.yith-wcwl-add-to-wishlist').length && ! $( '#yith-wcwl-popup-message' ).length ) {
            var message_div = $( '<div>' )
                    .attr( 'id', 'yith-wcwl-message' ),
                popup_div = $( '<div>' )
                    .attr( 'id', 'yith-wcwl-popup-message' )
                    .html( message_div )
                    .hide();

            $( 'body' ).prepend( popup_div );
        }
    }

    /**
     * Add Dropdown to Add to Wishlist when modal is disabled and Multi Wishlist enabled
     *
     * @return void
     * @since 3.0.0
     */
    function init_wishlist_dropdown() {
        $('.yith-wcwl-add-button').filter('.with-dropdown')
            .on( 'mouseleave', function(){
                var t = $(this),
                    dropdown = t.find('.yith-wcwl-dropdown');

                if( dropdown.length ) {
                    dropdown.fadeOut(200);
                }
            } )
            .children('a')
            .on( 'mouseenter', function(){
                var t = $(this),
                    el_wrap = t.closest('.with-dropdown'),
                    dropdown = el_wrap.find('.yith-wcwl-dropdown');

                if( dropdown.length && dropdown.children().length ){
                    el_wrap.find('.yith-wcwl-dropdown').fadeIn(200);
                }
            } );
    }

    /**
     * Handle Drag & Drop of wishlist items for sorting
     *
     * @return void
     * @since 3.0.0
     */
    function init_wishlist_drag_n_drop() {
        if( typeof yith_wcwl_l10n.enable_drag_n_drop === 'undefined' || ! yith_wcwl_l10n.enable_drag_n_drop ){
            return;
        }

        $('.wishlist_table').filter('.sortable').not('.no-interactions').each( function(){
            var t = $(this),
                jqxhr = false;

            t.sortable( {
                items: '[data-row-id]',
                scroll: true,
                helper: function( e, ui ){
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: function(){
                    var row = t.find('[data-row-id]'),
                        positions = [],
                        i = 0;

                    if( ! row.length ){
                        return;
                    }

                    if( jqxhr ){
                        jqxhr.abort();
                    }

                    row.each( function(){
                        var t = $(this);

                        t.find( 'input[name*="[position]"]' ).val(i++);

                        positions.push( t.data('row-id') );
                    } );

                    jqxhr = $.ajax({
                        data: {
                            action: yith_wcwl_l10n.actions.sort_wishlist_items,
                            positions: positions,
                            wishlist_token: t.data('token'),
                            page: t.data('page'),
                            per_page: t.data('per-page')
                        },
                        method: 'POST',
                        url: yith_wcwl_l10n.ajax_url
                    });
                }
            } );
        } );
    }

    /**
     * Handle quantity input change for each wishlist item
     *
     * @return void
     * @since 3.0.0
     */
    function init_quantity() {
        var jqxhr,
            timeout;

        $('.wishlist_table').on( 'change', '.product-quantity input', function(){
            var t = $(this),
                row = t.closest('[data-row-id]'),
                product_id = row.data('row-id'),
                table = t.closest('.wishlist_table'),
                token = table.data('token');

            clearTimeout( timeout );

            // set add to cart link to add specific qty to cart
            row.find( '.add_to_cart' ).data('quantity', t.val());

            timeout = setTimeout( function(){
                if( jqxhr ){
                    jqxhr.abort();
                }

                jqxhr = $.ajax({
                    beforeSend: function(){
                        block( table );
                    },
                    complete: function(){
                        unblock( table );
                    },
                    data: {
                        product_id: product_id,
                        wishlist_token: token,
                        quantity: t.val(),
                        action: yith_wcwl_l10n.actions.update_item_quantity
                    },
                    method: 'POST',
                    url: yith_wcwl_l10n.ajax_url
                });
            }, 1000 );
        } );
    }

    /**
     * Init handling for copy button
     *
     * @return void
     * @since 2.2.11
     */
    function init_copy_wishlist_link () {
        $('.copy-trigger').on('click', function () {

            var obj_to_copy = $('.copy-target');

            if (obj_to_copy.length > 0) {
                if (obj_to_copy.is('input')) {

                    if (isOS()) {

                        obj_to_copy[0].setSelectionRange(0, 9999);
                    } else {
                        obj_to_copy.select();
                    }
                    document.execCommand("copy");
                } else {

                    var hidden = $('<input/>', {
                        val : obj_to_copy.text(),
                        type: 'text'
                    });

                    b('body').append(hidden);

                    if (isOS()) {
                        hidden[0].setSelectionRange(0, 9999);
                    } else {
                        hidden.select();
                    }
                    document.execCommand('copy');

                    hidden.remove();
                }
            }
        });
    }

    /**
     * Handle popup for images grid layout
     *
     * @return void
     * @since 3.0.0
     */
    function init_wishlist_details_popup() {
        $('.wishlist_table').filter('.images_grid').not('.enhanced')
            .on( 'click', '[data-row-id] .product-thumbnail a', function(ev){
                var t = $(this),
                    item = t.closest('[data-row-id]'),
                    items = item.siblings( '[data-row-id]' ),
                    popup = item.find('.item-details');

                ev.preventDefault();

                if( popup.length ){
                    items.removeClass('show');
                    item.toggleClass( 'show' );
                }
            } )
            .on( 'click', '[data-row-id] a.close', function (ev){
                var t = $(this),
                    item = t.closest('[data-row-id]'),
                    popup = item.find('.item-details');

                ev.preventDefault();

                if( popup.length ){
                    item.removeClass('show');
                }
            } )
            .on( 'click', '[data-row-id] a.remove_from_wishlist', function(ev){
                var t = $(this);

                ev.stopPropagation();

                remove_item_from_wishlist( t );

                return false;
            } )
            .addClass( 'enhanced' );


        $(document)
            .on( 'click', function( ev ){
                if( ! $( ev.target ).closest( '[data-row-id]' ).length ){
                    $('.wishlist_table').filter('.images_grid').find('.show').removeClass('show');
                }
            } )
            .on( 'added_to_cart', function(){
                $('.wishlist_table').filter('.images_grid').find('.show').removeClass('show');
            } );
    }

    /**
     * Handle tabs inside wishlist popups
     *
     * @return void
     * @since 3.0.0
     */
    function init_wishlist_popup_tabs() {
        $(document).on( 'click', '.show-tab', function(ev){
            var t = $(this),
                container = t.closest('.yith-wcwl-popup-content'),
                tab = t.data('tab'),
                target = container.find('.tab').filter( '.' + tab );

            ev.preventDefault();

            if( ! target.length ){
                return false;
            }

            t.addClass('active').siblings('.show-tab').removeClass('active');

            target.show().siblings('.tab').hide();

            if( 'create' === tab ){
                container.prepend( '<input type="hidden" id="new_wishlist_selector" class="wishlist-select" value="new">' );
            }
            else{
                container.find( '#new_wishlist_selector' ).remove();
            }
        } );

        $(document).on( 'change', '.wishlist-select', function(ev){
            var t = $(this),
                container = t.closest('.yith-wcwl-popup-content'),
                tab = t.closest( '.tab' ),
                createTab = container.find( '.tab.create' ),
                showTab = container.find( '.show-tab' ),
                createShowTab = showTab.filter( '[data-tab="create"]' ),
                val = t.val();

            if( val === 'new' && createTab.length ){
                tab.hide();
                createTab.show();

                showTab.removeClass( 'active' );
                createShowTab.addClass( 'active' );

                t.find('option').removeProp( 'selected' );
                t.change();
            }
        } )
    }

    /**
     * Init responsive behaviour of the wishlist
     *
     * @return void
     * @since 3.0.0
     */
    function init_wishlist_responsive() {
        var jqxhr = false;

        if( ! yith_wcwl_l10n.is_wishlist_responsive ){
            return;
        }

        $( window ).on( 'resize', function( ev ){
            var table = $('.wishlist_table.responsive'),
                mobile = table.is('.mobile'),
                media = window.matchMedia( '(max-width: 768px)' ),
                form = table.closest('form'),
                id = form.attr('class'),
                options = form.data('fragment-options'),
                fragments = {},
                load = false;

            if( ! table.length ){
                return;
            }

            if( media.matches && table && ! mobile ){
                options.is_mobile = 'yes';
                load = true;
            }
            else if( ! media.matches && table && mobile ){
                options.is_mobile = 'no';
                load = true;
            }

            if( load ){
                if( jqxhr ){
                    jqxhr.abort();
                }

                fragments[ id ] = options;

                jqxhr = $.ajax( {
                    beforeSend: function(){
                        block( table );
                    },
                    complete: function(){
                        unblock( table );
                    },
                    data: {
                        action: yith_wcwl_l10n.actions.load_mobile_action,
                        fragments: fragments
                    },
                    method: 'post',
                    success: function( data ){
                        if( typeof data.fragments != 'undefined' ){
                            replace_fragments( data.fragments );

                            init_handling_after_ajax();

                            $(document).trigger( 'yith_wcwl_responsive_template', [ mobile, data.fragments ] );
                        }
                    },
                    url: yith_wcwl_l10n.ajax_url
                } );
            }
        } );
    }

    /* === EVENT HANDLING === */

    /**
     * Move item to another wishlist
     *
     * @return void
     * @since 3.0.0
     */
    function call_ajax_move_item_to_another_wishlist( data, beforeSend, complete ) {
        data.action = yith_wcwl_l10n.actions.move_to_another_wishlist_action;

        if( data.wishlist_token === '' || data.destination_wishlist_token === '' || data.item_id === '' ){
            return;
        }

        $.ajax( {
            beforeSend: beforeSend,
            url: yith_wcwl_l10n.ajax_url,
            data: data,
            dataType: 'json',
            method: 'post',
            success: function( response ){
                complete( response );

                init_handling_after_ajax();

                $('body').trigger('moved_to_another_wishlist', [ $(this), data.item_id ] );
            }
        });
    }

    /**
     * Remove a product from the wishlist.
     *
     * @param el
     * @return void
     * @since 1.0.0
     */
    function remove_item_from_wishlist( el ) {
        var table = el.parents( '.cart.wishlist_table' ),
            row = el.parents( '[data-row-id]' ),
            data_row_id = row.data( 'row-id'),
            wishlist_id = table.data( 'id' ),
            wishlist_token = table.data( 'token' ),
            data = {
                action: yith_wcwl_l10n.actions.remove_from_wishlist_action,
                remove_from_wishlist: data_row_id,
                wishlist_id: wishlist_id,
                wishlist_token: wishlist_token,
                fragments: retrieve_fragments( data_row_id )
            };

        $.ajax( {
            beforeSend: function(){
                block( table );
            },
            complete: function(){
                unblock( table );
            },
            data: data,
            method: 'post',
            success: function( data ){
                if( typeof data.fragments != 'undefined' ){
                    replace_fragments( data.fragments );
                }

                init_handling_after_ajax();

                $('body').trigger('removed_from_wishlist', [ el, row ] );
            },
            url: yith_wcwl_l10n.ajax_url
        } );
    }

    /**
     * Remove a product from the wishlist.
     *
     * @param el
     * @param form
     * @return void
     * @since 1.0.0
     */
    function reload_wishlist_and_adding_elem( el, form ) {

        var product_id = el.data( 'data-product-id' ),
            table = $(document).find( '.cart.wishlist_table' ),
            pagination = table.data( 'pagination' ),
            per_page = table.data( 'per-page' ),
            wishlist_id = table.data( 'id' ),
            wishlist_token = table.data( 'token' ),
            data = {
                action: yith_wcwl_l10n.actions.reload_wishlist_and_adding_elem_action,
                pagination: pagination,
                per_page: per_page,
                wishlist_id: wishlist_id,
                wishlist_token: wishlist_token,
                add_to_wishlist: product_id,
                product_type: el.data( 'product-type' )
            };

        if( ! is_cookie_enabled() ){
            alert( yith_wcwl_l10n.labels.cookie_disabled );
            return
        }

        $.ajax({
            type: 'POST',
            url: yith_wcwl_l10n.ajax_url,
            data: data,
            dataType    : 'html',
            beforeSend: function(){
                block( table );
            },
            complete: function(){
                unblock( table );
            },
            success: function(res) {
                var obj      = $(res),
                    new_form = obj.find('#yith-wcwl-form'); // get new form

                form.replaceWith( new_form );
                init_handling_after_ajax();
            }
        });
    }

    /**
     * Show form to edit wishlist title
     *
     * @param ev event
     * @return void
     * @since 2.0.0
     */
    function show_title_form( ev ) {
        var t = $(this),
            table = t.closest( '.wishlist_table' ),
            title = null;
        ev.preventDefault();

        // if button is in table
        if( table.length ){
            title = t.closest('[data-wishlist-id]').find('.wishlist-title');
        }
        else{
            title = t.parents( '.wishlist-title' );
        }

        title.next().show().find('input[type="text"]').focus();
        title.hide();
    }

    /**
     * Hide form to edit wishlist title
     *
     * @param ev event
     * @return void
     * @since 2.0.0
     */
    function hide_title_form( ev ) {
        var t = $(this);
        ev.preventDefault();

        t.parents( '.hidden-title-form').hide();
        t.parents( '.hidden-title-form').prev().show();
    }

    /**
     * Submit form to save a new wishlist title
     *
     * @param ev event
     * @return void
     * @since 2.0.7
     */
    function submit_title_form( ev ) {
        var t = $(this),
            form = t.closest( '.hidden-title-form' ),
            row = t.closest( '[data-wishlist-id]' ),
            wishlist_id = row.data( 'wishlist-id' ),
            title_input = form.find( 'input[type="text"]' ),
            new_title = title_input.val(),
            data = {};

        ev.preventDefault();

        if( ! new_title ){
            form.addClass('woocommerce-invalid');
            title_input.focus();
            return;
        }

        data = {
            action: yith_wcwl_l10n.actions.save_title_action,
            wishlist_id: wishlist_id,
            title: new_title,
            fragments: retrieve_fragments()
        };

        $.ajax({
            type: 'POST',
            url: yith_wcwl_l10n.ajax_url,
            data: data,
            dataType: 'json',
            beforeSend: function(){
                block( form );
            },
            complete: function(){
                unblock( form );
            },
            success: function( response ) {
                var fragments = response.fragments,
                    status = response.result;

                if( status ) {
                    form.hide();
                    form.prev().find('.wishlist-anchor').text( new_title ).end().show();
                }
                else{
                    form.addClass( 'woocommerce-invalid' );
                    title_input.focus();
                }

                if( typeof fragments != 'undefined' ){
                    replace_fragments( fragments );
                }
            }
        });
    }

    /**
     * Submit form to save a new wishlist privacy
     *
     * @param ev event
     * @return void
     * @since 2.0.7
     */
    function save_privacy( ev ){
        var t = $(this),
            new_privacy = t.val(),
            row = t.closest( '[data-wishlist-id]' ),
            wishlist_id = row.data( 'wishlist-id' ),
            data = {
                action: yith_wcwl_l10n.actions.save_privacy_action,
                wishlist_id: wishlist_id,
                privacy: new_privacy,
                fragments: retrieve_fragments()
            };

        $.ajax({
            type: 'POST',
            url: yith_wcwl_l10n.ajax_url,
            data: data,
            dataType: 'json',
            success: function( response ) {
                var fragments = response.fragments;

                if( typeof fragments != 'undefined' ){
                    replace_fragments( fragments );
                }
            }
        });
    }

    /* === UTILS === */

    /**
     * Closes pretty photo popup, if any
     *
     * @return void
     * @since 3.0.0
     */
    function close_pretty_photo( message ) {
        if( typeof $.prettyPhoto != 'undefined' && typeof $.prettyPhoto.close != 'undefined' ) {
            if( typeof message != 'undefined' ){
                var container = $('.pp_content_container'),
                    content = container.find('.pp_content'),
                    form = container.find('.yith-wcwl-popup-form'),
                    popup = form.closest( '.pp_pic_holder' );

                if( form.length ){
                    var new_content = $( '<div/>', {
                        class: 'yith-wcwl-popup-feedback'
                    } );

                    new_content.append( $( '<i/>', { class: 'fa fa-check heading-icon' } ) );
                    new_content.append( $( '<p/>', { class: 'feedback', html: message } ) );
                    new_content.css( 'display', 'none' );

                    content.css( 'height', 'auto' );

                    form.after( new_content );
                    form.fadeOut( 200, function(){
                        new_content.fadeIn();
                    } );

                    popup.addClass( 'feedback' );
                    popup.css( 'left', ( ( $( window ).innerWidth() / 2 ) - ( popup.outerWidth() / 2 ) ) + 'px' );

                    if( typeof yith_wcwl_l10n.auto_close_popup == 'undefined' || yith_wcwl_l10n.auto_close_popup ) {
                        setTimeout(close_pretty_photo, yith_wcwl_l10n.popup_timeout);
                    }
                }
            }
            else{
                try {
                    $.prettyPhoto.close();
                }
                catch( e ){ /* do nothing, no popup to close */ }
            }
        }
    }

    /**
     * Print wishlist message for the user
     *
     * @var response_message string Message to print
     * @return void
     * @since 3.0.0
     */
    function print_message( response_message ) {
        var msgPopup = $( '#yith-wcwl-popup-message' ),
            msg = $( '#yith-wcwl-message' ),
            timeout = typeof yith_wcwl_l10n.popup_timeout != 'undefined' ? yith_wcwl_l10n.popup_timeout : 3000;

        if( typeof yith_wcwl_l10n.enable_notices != 'undefined' && ! yith_wcwl_l10n.enable_notices ){
            return;
        }

        msg.html( response_message );
        msgPopup.css( 'margin-left', '-' + $( msgPopup ).width() + 'px' ).fadeIn();
        window.setTimeout( function() {
            msgPopup.fadeOut();
        }, timeout );
    }

    /**
     * Update lists after a new list is added
     *
     * @param wishlists array Array of wishlists
     * @return void
     * @since 3.0.0
     */
    function update_wishlists( wishlists ) {
        var wishlist_select = $( 'select.wishlist-select' ),
            wishilst_dropdown = $( 'ul.yith-wcwl-dropdown' );

        // update options for all wishlist selects
        wishlist_select.each( function(){
            var t = $(this),
                wishlist_options = t.find( 'option' ),
                new_option = wishlist_options.filter( '[value="new"]' );

            wishlist_options.not(new_option ).remove();

            $.each( wishlists, function( i, v ){
                $('<option>', { value: v.id, html: v.wishlist_name } ).appendTo(t);
            } );

            t.append( new_option );
        } );

        // update options for all wishlist dropdown
        wishilst_dropdown.each( function(){
            var t = $(this),
                wishlist_options = t.find( 'li' ),
                container = t.closest( '.yith-wcwl-add-button' ),
                default_button = container.children( 'a.add_to_wishlist' ),
                product_id = default_button.attr('data-product-id'),
                product_type = default_button.attr('data-product-type');

            wishlist_options.remove();
            $.each( wishlists, function( i, v ) {
                $('<li>').append( $('<a>', {
                    rel: 'nofollow',
                    html: v.wishlist_name,
                    class: 'add_to_wishlist',
                    href: v.add_to_this_wishlist_url,
                    'data-product-id': product_id,
                    'data-product-type': product_type,
                    'data-wishlist-id': v.id
                } ) ).appendTo(t);
            } );
        } );
    }

    /**
     * Block item if possible
     *
     * @param item jQuery object
     * @return void
     * @since 3.0.0
     */
    function block( item ) {
        if( typeof $.fn.block != 'undefined' ) {
            item.fadeTo('400', '0.6').block( {
                message: null,
                overlayCSS : {
                    background    : 'transparent url(' + yith_wcwl_l10n.ajax_loader_url + ') no-repeat center',
                    backgroundSize: '40px 40px',
                    opacity       : 1
                }
            } );
        }
    }

    /**
     * Unblock item if possible
     *
     * @param item jQuery object
     * @return void
     * @since 3.0.0
     */
    function unblock( item ) {
        if( typeof $.fn.unblock != 'undefined' ) {
            item.stop(true).css('opacity', '1').unblock();
        }
    }

    /**
     * Check if cookies are enabled
     *
     * @return bool
     * @since 2.0.0
     */
    function is_cookie_enabled() {
        if (navigator.cookieEnabled) return true;

        // set and read cookie
        document.cookie = "cookietest=1";
        var ret = document.cookie.indexOf("cookietest=") !== -1;

        // delete cookie
        document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";

        return ret;
    }

    /**
     * Retrieve fragments that need to be refreshed in the page
     *
     * @param search string Ref to search among all fragments in the page
     * @return object Object containing a property for each fragments that matches search
     * @since 3.0.0
     */
    function retrieve_fragments( search ) {
        var options = {},
            fragments = null;

        if( search ){
            if( typeof search === 'object' ){
                search = $.extend( {
                    s: '',
                    container: $(document),
                    firstLoad: false
                }, search );

                fragments = search.container.find( '.wishlist-fragment' );

                if( search.s ){
                    fragments = fragments.not('[data-fragment-ref]').add(fragments.filter('[data-fragment-ref="' + search.s + '"]'));
                }

                if( search.firstLoad ){
                    fragments = fragments.filter( '.on-first-load' );
                }
            }
            else {
                fragments = $('.wishlist-fragment');

                if (typeof search == 'string' || typeof search == 'number') {
                    fragments = fragments.not('[data-fragment-ref]').add(fragments.filter('[data-fragment-ref="' + search + '"]'));
                }
            }
        }
        else{
            fragments = $('.wishlist-fragment');
        }

        fragments.each( function(){
            var t = $(this),
                id = t.attr('class');

            options[ id ] = t.data('fragment-options');
        } );

        return options;
    }

    /**
     * Load fragments on page loading
     *
     * @param search string Ref to search among all fragments in the page
     * @since 3.0.0
     */
    function load_fragments( search ) {
        if( ! yith_wcwl_l10n.enable_ajax_loading ){
            return;
        }

        search = $.extend( {
            firstLoad: true
        }, search );

        var fragments = retrieve_fragments( search );

        if( ! fragments ){
            return;
        }

        $.ajax( {
            data: {
                action: yith_wcwl_l10n.actions.load_fragments,
                fragments: fragments
            },
            method: 'post',
            success: function( data ){
                if( typeof data.fragments != 'undefined' ){
                    replace_fragments( data.fragments );

                    init_handling_after_ajax();

                    $(document).trigger( 'yith_wcwl_fragments_loaded', [ fragments, data.fragments ] );
                }
            },
            url: yith_wcwl_l10n.ajax_url
        } );
    }

    /**
     * Replace fragments with template received
     *
     * @param fragments array Array of fragments to replace
     * @since 3.0.0
     */
    function replace_fragments( fragments ) {
       $.each( fragments, function( i, v ){
           var itemSelector = '.' + i.split( ' ' ).filter( function(val){ return val.length && val !== 'exists' } ).join( '.' ),
               toReplace = $( itemSelector );

           // find replace tempalte
           var replaceWith = $(v).filter( itemSelector );

           if( ! replaceWith.length ){
               replaceWith = $(v).find( itemSelector );
           }

           if( toReplace.length && replaceWith.length ){
               toReplace.replaceWith( replaceWith );
           }
       } ) ;
    }

    /**
     * Check if device is an IOS device
     * @since 2.2.11
     */
    function isOS() {
        return navigator.userAgent.match(/ipad|iphone/i);
    }
});
