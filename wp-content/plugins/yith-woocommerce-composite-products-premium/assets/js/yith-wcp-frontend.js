/**
 * Frontend
 *
 * @author Your Inspiration Themes
 * @package YITH Composite Products for WooCommerce
 * @version 1.0.0
 */
jQuery(document).ready( function($) {
    "use strict";

    if ( typeof yith_wcp_general === 'undefined' ) {
        return false;
    }

    var $ywcp_global_exclusive_selected_ids = new Array();
    var $ywcp_global_dependece_selected_disabled_ids = new Array();
    var $ywcp_global_current_step = 1;
    var $ywcp_dependencied_data = getDependenciesData();

    function ywcp_initialize() {
        'use strict';

        // Initialize
        checkReEditCartValues();
        applyDependenciesData();
        checkRequiredComponent();
        loadComponentsTotal();
        showCurrentComponentItem();

        // Pagination
        $(document).on( 'click' , '.ywcp_component_options_list_container .woocommerce-pagination ul.page-numbers > li' , function(){
            'use strict';

            var $page_link =  $(this).find('a.page-numbers');

            if( $page_link.length > 0 ) {

                var $product_id = $(this).closest('.ywcp_components_container').data('product-id');
                var $key = $(this).closest('.ywcp_components_single_item').data('component-key');
                var $component_container = $(this).closest('.ywcp_component_options_list_container');

                showLoader( $component_container );

                var $current_page = $(this).closest('.woocommerce-pagination').find('span.page-numbers.current').html();

                if ($page_link.hasClass('prev')) {
                    var $clicked_page = parseInt($current_page) - 1;
                } else if ($page_link.hasClass('next')) {
                    var $clicked_page = parseInt($current_page) + 1;
                } else {
                    var $clicked_page = $page_link.html();
                }

                if( $clicked_page > 0 ) {

                    $.ajax({
                        url: yith_wcp_general.wc_ajax_url.toString().replace( '%%endpoint%%', 'ywcp_component_items_page_changed' ),
                        type: 'POST',
                        data: {
                            product_id       : $product_id,
                            ywcp_current_page: $clicked_page,
                            key              : $key
                        },
                        beforeSend: function(){
                        },
                        success: function( res ){
                            $component_container.html( res );
                            $(document).trigger('ywcp_component_item_page_loaded' , $component_container );
                            hideLoader( $component_container );
                        }
                    });

                }
            }

            return false;

        });

        $(document).on( 'ywcp_component_item_page_loaded' , function( e , data ) {
            'use strict';

            // reapply exclusion list
            reApplyExclusionList( data );

            // reapply dependencies data
            applyDependenciesData();

        } );

        // Show selection
        $(document).on( 'click' , '.ywcp_accordion div:not(.ywcp_selection_selected).ywcp_components_single_item > h3' , function(){
            'use strict';

            var $container_single_item = $(this).closest('.ywcp_components_single_item');

            if ( $container_single_item.hasClass('ywcp_selection_opened') ) {

                var $selection_close = $container_single_item.find('.ywcp_selection_close');
                if ( $selection_close.length > 0 ) { $selection_close.trigger('click'); }

            } else {

                var $selection_open = $container_single_item.find('.ywcp_selection_open');
                if ( $selection_open.length > 0 ) { $selection_open.trigger('click'); }

            }

            return false;

        });

        $(document).on( 'click' , '.ywcp_selection_open' , function(){
            'use strict';

            var $container_single_item = $(this).closest('.ywcp_components_single_item');
            $container_single_item.addClass('ywcp_selection_opened')
            $container_single_item.trigger('ywcp_component_selection_opened');
            return false;

        });

        // Hide selection
        $(document).on( 'click' , '.ywcp_selection_close' , function(){
            'use strict';

            var $container_single_item = $(this).closest('.ywcp_components_single_item');
            $container_single_item.removeClass('ywcp_selection_opened')
            $container_single_item.trigger('ywcp_component_selection_closed');
            return false;

        });

        // Clear selection
        $(document).on( 'click' , '.ywcp_selection_clear' , function(){
            'use strict';

            var $container_single_item = $(this).closest('.ywcp_components_single_item');
            clearSelection( $container_single_item );
            var $selection_open = $container_single_item.find('.ywcp_selection_open');
            if ( $selection_open.length > 0 ) { $selection_open.trigger('click'); }
            $container_single_item.trigger('ywcp_component_selection_cleared');
            return false;

        });

        // do selection

        $(document).on( 'change' , '.ywcp_component_otpions_select, .ywcp_component_options_radio_input_container input[type="radio"]' , function() {
            'use strict';

            $(this).trigger('ywcp_component_do_selection' , $(this) );
            return false;

        });

        $(document).on( 'click' , '.ywcp_component_otpions_thumbnails_container ul.products li' , function(){
            'use strict';

            $(this).trigger('ywcp_component_do_selection' , $(this) );
            return false;

        });

        $(document).on( 'ywcp_component_do_selection' , function( e , data ){
            'use strict';

            var $selection_item = $( data );
            var $container_single_item = $selection_item.closest('.ywcp_components_single_item');

            showLoader( $container_single_item );

            var $product_id = 0;

            if ( $selection_item.val() > 0 ) {  // select, radio
                $product_id = $selection_item.val();
            } else if( $selection_item.data('product-id') > 0 ) { // product thumbnails
                $product_id = $selection_item.data('product-id');
            }

            if ( $selection_item.hasClass('out-of-stock') ) {
                hideLoader( $container_single_item );
            } else if ( $product_id > 0 ) {
                setSelection( $product_id, $container_single_item, true, true, true )
            } else {
                removeSelection( $container_single_item );
                hideLoader( $container_single_item );
            }

        });

        // Out of stock variations
        $(document).on('change', '.ywcp .variations select', function(){
            var text = $('.ywcp_inner_selected_container .woocommerce-variation-availability .out-of-stock').text();
            if ( text !== '' ) { $('.single_add_to_cart_button').prop('disabled', true); }
        });


        $(document).on( 'ywcp_component_item_selected_loaded ywcp_component_selection_cleared', function( e , data ) {
            'use strict';

            var $selected_single_item = $(data);

            // required field
            checkRequiredComponent();

            // exclusive product
            checkExclusiveSelection( $selected_single_item );

            // check dependencies
            applyDependenciesData();

            // Update Total
            loadComponentsTotal();

        });

        // Total

        // Variations

        $(document).on( 'found_variation', function( event, variation ) {
            'use strict';

            // variation changed
            var $selected_container = $( event.target );

            // Variations Discount Fix
            var ywcp_discount = $selected_container.parent().parent().data('discount');

            var ywcp_variation_display_price = 0;
            if ( ywcp_discount > 0 ) {
                ywcp_variation_display_price = variation.display_price / 100 * ( 100 - ywcp_discount );
            } else {
                ywcp_variation_display_price = variation.display_price;
            }

            // Replace variation image
            var rep_product_image = $selected_container.find( '.attachment-shop_thumbnail' );
            rep_product_image.attr('origin', rep_product_image.attr('src') );
            var rep_selected_id = $selected_container.find('.variation_id').val();
            var rep_variation_id = 0;
            var rep_variation_img = 0;
            for ( var x = 0; x < $selected_container.data( 'product_variations').length; x++ ) {
                rep_variation_id = $selected_container.data( 'product_variations')[x].variation_id;
                rep_variation_img = $selected_container.data( 'product_variations')[x].image_src;
                if ( rep_selected_id == rep_variation_id ) {
                    rep_product_image.attr('src', rep_variation_img );
                    rep_product_image.attr('srcset', rep_variation_img );
                }
            }

            $selected_container.data( 'selected-price', ywcp_variation_display_price );
            $selected_container.attr( 'data-selected-price', ywcp_variation_display_price );

            applyDependenciesData();

            checkRequiredComponent();

            loadComponentsTotal();

        } );

        $(document).on( 'hide_variation', function( event ) {
            'use strict';

            var $single_variation = $( event.target );
            var $selected_container = $single_variation.closest( '.ywcp_inner_selected_container' );
            $selected_container.data( 'selected-price' , 0 );
            $selected_container.attr( 'data-selected-price' , 0 );
            applyDependenciesData();
            checkRequiredComponent();
            loadComponentsTotal();

        } );

        // Quantity
        $(document).on( 'change', 'form.cart.ywcp > div.quantity input.qty, .ywcp_inner_selected_container div.quantity input.qty', function( e ) {
            'use strict';

            loadComponentsTotal();

        } );

        $(document).on( 'ywcp_calculate_total', function( e ) {
            'use strict';

            loadComponentsTotal();

        } );

        // step navigation

        $(document).on( 'click' , '.ywcp_step_prev' , function() {
            'use strict';

            if ( $ywcp_global_current_step > 0 ) {

                var $global_container = $( '.ywcp_components_container.ywcp_step' );
                var $components_items = $global_container.find( '.ywcp_components_single_item' );
                var $ywcp_current_step_index = $ywcp_global_current_step - 1;

                if ( $ywcp_current_step_index >= 0 ) {

                    // do next if required component is selected
                    var $ywcp_current_components_item = $components_items.eq( ( $ywcp_current_step_index ) );
                    var $item_is_required_not_selected = checkSingleRequiredComponents( $ywcp_current_components_item, true );

                    // ignore required component if click prev button
                    if ( true || ! $item_is_required_not_selected ) {

                        $ywcp_global_current_step -= 1;
                        showCurrentComponentItem();

                    }

                }

            }

            return false;

        } );

        $(document).on( 'click' , '.ywcp_step_next' , function() {
            'use strict';

            if ( $ywcp_global_current_step > 0 ) {

                var $global_container = $( '.ywcp_components_container.ywcp_step' );
                var $components_items = $global_container.find( '.ywcp_components_single_item' );
                var $ywcp_current_step_index = $ywcp_global_current_step - 1;

                if ( $ywcp_current_step_index >= 0 ) {

                    // do next if required component is selected
                    var $ywcp_current_components_item = $components_items.eq( ( $ywcp_current_step_index ) );
                    var $item_is_required_not_selected = checkSingleRequiredComponents( $ywcp_current_components_item, true );

                    if ( ! $item_is_required_not_selected ) {

                        $ywcp_global_current_step += 1;
                        showCurrentComponentItem();

                    }

                }


            }

            return false;

        } );

        // show components when all is loaded

        $('.ywcp_form_loading_message').hide();
        $('form.cart.ywcp').addClass('ywcp_form_loaded');

    }

    ywcp_initialize();

    /* AutoSelect Unique Elements */
    function selectUniqueElements() {
        $('.ywcp_component_otpions_thumbnails_container ul.products li').each( function() {
            if ( $(this).siblings().length == 0 ) {
                $(this).trigger('ywcp_component_do_selection', $(this) );
            }
        });
        // Select
        $('.ywcp_component_otpions_select option').each( function() {
            if ( $(this).siblings().length == 1 ) {
                $(this).trigger('ywcp_component_do_selection', $(this) );
            }
        });
        // Radio
        $('.ywcp_components_container input[type="radio"]').each( function() {
            if ( $(this).siblings().length == 1 ) {
                $(this).trigger('ywcp_component_do_selection', $(this) );
            }
        });
    }
    $( document ).ready(function() {
        // selectUniqueElements();
    });

    /* YITH Quick View */
    $(document).on( 'qv_loader_stop yit_quick_view_loaded', function( ) {
        ywcp_initialize();
    });

    /* utility */

    /**
     *
     * @param $disable_element
     * @param $load_element
     */
    function showLoader( $load_element ) {
        'use strict';

        $load_element.block({ message: '' ,   overlayCSS:  {
            backgroundColor: '#fff',
            opacity:         0.6,
            cursor:          'wait'
        } });

    }

    function hideLoader( $load_element ) {
        'use strict';

        $load_element.unblock();

    }

    function clearSelection( $container_single_item ) {

        removeSelection( $container_single_item );
        removeSelectionInput( $container_single_item );

    }

    function removeSelection( $container_single_item ) {
        'use strict';

        var $container_list = $container_single_item.find('.ywcp_component_options_list_container');
        var $container_selection = $container_single_item.find('.ywcp_component_options_selection_container');

        $container_list.find('select').val('-1');

        // reset thumbnail
        if ( $container_selection.hasClass('ywcp_thumb_replace') ) {
            var component_thumb = $container_selection.attr( 'thumb-replaced' );
            var replaced_thumb = jQuery('.woocommerce-product-gallery__image > img').attr('src');
            if ( component_thumb == replaced_thumb ) {
                $container_selection.removeAttr( 'thumb-replaced' );
                var default_thumb = jQuery('.woocommerce-product-gallery__image > img').attr('default-thumb');
                if ( typeof default_thumb !== typeof undefined && default_thumb !== false ) {
                    jQuery('.woocommerce-product-gallery__image img').attr('src', default_thumb );
                    jQuery('.woocommerce-product-gallery__image a img').attr('srcset', default_thumb );
                }
            }
        }

        $container_single_item.removeClass('ywcp_selection_selected');

        removeExclusiveSelection( $container_single_item, $container_selection.data('selected-id') );

        $container_selection.data( 'selected-id', '' );
        $container_selection.removeAttr( 'data-selected-id' );

        $container_selection.html('');

        var $subtotal_container = $container_single_item.find('.ywcp_component_subtotal');
        var $subtotal_value = $subtotal_container.find('span.amount');

        $subtotal_container.hide();
        $subtotal_value.html('');
    }

    function removeSelectionInput( $container_single_item ) {
        'use strict';

        var $select_input = $container_single_item.find('.ywcp_component_otpions_select');
        if( typeof $select_input != 'undefined' ) {
            $select_input.val('-1');
        }

        var $radio_input = $container_single_item.find('.ywcp_component_options_radio_container input.ywcp_radio_default_value');

        if( typeof $radio_input != 'undefined' ) {
            $radio_input.attr('checked', true);
        }

    }

    function setSelection( $product_id , $container_single_item , $trigger_item_selected_loaded , $do_next_step , $do_hide_loader ) {

        if( ! $container_single_item.hasClass( 'ywcp_selection_selected' ) ) {

            var $key = $container_single_item.data('component-key');

            var $master_id = $container_single_item.closest('.ywcp_components_container').data('product-id');

            $container_single_item.removeClass('ywcp_selection_opened') ;
            $container_single_item.removeClass('ywcp_components_required_blocked')
            $container_single_item.addClass('ywcp_selection_selected');

            // load product preview via ajax
            $.ajax({
                url: yith_wcp_general.wc_ajax_url.toString().replace( '%%endpoint%%', 'ywcp_component_items_selected' ),
                type: 'POST',
                dataType: 'json',
                data: {
                    master_id : $master_id,
                    product_id: $product_id,
                    key       : $key
                },
                beforeSend: function(){
                },
                success: function( res ){

                    if ( res != null ) {

                        var $container_selection = $container_single_item.find('.ywcp_component_options_selection_container');

                        $container_selection.html( res.html );

                        if ( res.html.match(/src="([^"]*)/) != null ) {

                            // thumbnail replace
                            if ( $container_selection.hasClass('ywcp_thumb_replace') ) {
                                var href = res.html.match(/src="([^"]*)/)[1];
                                var href_array = href.split( '-');
                                var href_size = href_array[ href_array.length-1 ];
                                var href_ext = href_size.split( '.' );
                                var new_image = href.replace( '-' + href_ext[0], '' );
                                $container_selection.attr( 'thumb-replaced', new_image );
                                var default_thumb = jQuery('.woocommerce-product-gallery__image > img').attr('default-thumb');
                                if ( ! ( typeof default_thumb !== typeof undefined && default_thumb !== false ) ) {
                                    var default_thumb = jQuery('.woocommerce-product-gallery__image img').attr('src');
                                    jQuery('.woocommerce-product-gallery__image > img').attr('default-thumb', default_thumb );
                                }
                                jQuery('.woocommerce-product-gallery__image img').attr('src', new_image );
                                jQuery('.woocommerce-product-gallery__image a img').attr('srcset', new_image );
                            }
                            // thumbnail replace

                        }

                        $container_selection.data( 'selected-id' , $product_id );
                        $container_selection.attr( 'data-selected-id' , $product_id );

                        var $container_inner_selected = $container_selection.find( '.ywcp_inner_selected_container' );

                        var $variation_select = $container_inner_selected.find( '.variations select' )

                        if ( $variation_select.length > 0 ) {
                            $container_inner_selected.wc_variation_form();
                            // Fire change in order to save 'variation_id' input.
                            $variation_select.change();
                        }

                        if ( $trigger_item_selected_loaded ) {
                            $(document).trigger('ywcp_component_item_selected_loaded', $container_single_item );
                        } else {
                            checkRequiredComponent();
                            checkExclusiveSelection( $container_single_item );
                            loadComponentsTotal();
                        }

                        // colors and label variation / product addons
                        if ( typeof $.yith_wccl != 'undefined' && res.attr_wccl ) {
                            $.yith_wccl( res.attr_wccl );
                        }

                        // automatic next step(not if is a varible product) : todo option
                        /*
                        if( $do_next_step && $container_inner_selected.data('product-type') != 'variable' ) {
                            nextStepAfterSelection();
                        }
                        */

                        // hide loader
                        if( $do_hide_loader ) {
                            hideLoader( $container_single_item );
                        }

                    }

                }
            });

        }

    }

    function checkRequiredComponent() {
        'use strict';

        var $global_container = $('.ywcp_components_container');

        var exist_required_not_selected = false;

        $global_container.find( '.ywcp_components_single_item.ywcp_components_required' ).each( function() {

            exist_required_not_selected = checkSingleRequiredComponents( $(this), true );

            if( exist_required_not_selected ) {
                return false;
            }

        } );

        var $add_to_cart = $global_container.closest('form.cart').find('button.single_add_to_cart_button');

        if( ! exist_required_not_selected ) {
            $add_to_cart.removeAttr('disabled');
        } else {
            $add_to_cart.attr('disabled', 'disabled' );
        }

        // Reques a Quote button
        var $request_a_quote_btn = $('a.add-request-quote-button');

        if( $request_a_quote_btn.length > 0 ) {

            if( ! exist_required_not_selected ) {
                $request_a_quote_btn.attr('href' , '#' );
                $request_a_quote_btn.removeClass('disabled');
            } else {
                $request_a_quote_btn.attr('href' , 'javascript:void()' );
                $request_a_quote_btn.addClass('disabled');
            }

        }
        // End Request a Quote button

        checkAdivceMessage();

        $(document).trigger('ywcp_component_add_to_cart_changed' , $add_to_cart );

    }

    function checkAdivceMessage() {
        'use strict';

        var $blocked_single_items = $('.ywcp_components_required_blocked');
        var $ywcp_customer_advice = $('.ywcp_customer_advice');

        if( $blocked_single_items.length > 0 ) {

            var $ywcp_customer_advice_component_list = $ywcp_customer_advice.find('.ywcp_customer_advice_component_list');

            $ywcp_customer_advice_component_list.html('');

            var $component_required_list='';

            $blocked_single_items.each( function() {

                $component_required_list+=$(this).data('title');

            } );

            $ywcp_customer_advice_component_list.html( $component_required_list );

            $ywcp_customer_advice.show();

        } else {

            $ywcp_customer_advice.hide();
        }
    }

    function checkSingleRequiredComponents( $single_item , $add_class ){

        if( $single_item.hasClass( 'ywcp_components_required' ) ) {

            var $required = $single_item.data('required');
            var $selected = getComponentSelectedValue( $single_item ) > 0 ;

            if( $required && ! $selected ) {

                if( $add_class ) {

                    $single_item.addClass('ywcp_components_required_blocked');

                }

                return true;

            }  else {

                $single_item.removeClass('ywcp_components_required_blocked');

            }

        }

        return false;
    }

    function getComponentSelectedValue( $single_item ) {
        'use strict';

        var $selected = false;
        var $selection_item_container = $single_item.find('.ywcp_component_options_selection_container');

        if( typeof $selection_item_container != 'undefined' ) {

            var $selected_value = parseInt( $selection_item_container.data('selected-id') );

            $selected = $selected_value > 0;

            // check if variation is selected
            if ( $selected ) {
                var $product_type =  $selection_item_container.find('.ywcp_inner_selected_container').data( 'product-type' );

                if ( $product_type == 'variable' ) {

                    var selected_variation_value = parseInt( $selection_item_container.find('.variation_id').val() );

                    if ( selected_variation_value > 0 ) {
                        return selected_variation_value;
                    } else {
                        return 0;
                    }

                }

                return $selected_value
            }
            // end check variation
        }

        return 0;
    }

    function getMasterVariableComponentSelectedValue( $single_item ) {
        'use strict';

        var $selected = false;
        var $selection_item_container = $single_item.find('.ywcp_component_options_selection_container');

        if ( typeof $selection_item_container != 'undefined' ) {

            var $selected_value = parseInt( $selection_item_container.data('selected-id') );

            if ( $selected_value > 0 ) {

                return $selected_value;

            }
            // end check variation
        }

        return 0;
    }

    function checkExclusiveSelection( $selected_single_item ) {
        'use strict';

        if( $selected_single_item.data('exclusive') ) {

            var $selection_item_container = $selected_single_item.find('.ywcp_component_options_selection_container');

            if ( typeof $selection_item_container != 'undefined' ) {

                var $exclusive_product_id = parseInt( $selection_item_container.data('selected-id') );

                if ( $exclusive_product_id > 0 ) {

                    var $exclusive_component_key = $selected_single_item.data('component-key');

                    $ywcp_global_exclusive_selected_ids[$exclusive_component_key] = $exclusive_product_id;

                    // Check all selected products

                    var $global_container = $('.ywcp_components_container');

                    $global_container.find( '.ywcp_components_single_item' ).each( function() {

                        var $single_item = $(this);

                        var $current_component_key = $single_item.data('component-key');

                        if ( $current_component_key != $exclusive_component_key ) {

                            var $current_single_item_container =  $single_item.closest('.ywcp_components_single_item');

                            if( typeof $current_single_item_container != 'undefined' ) {

                                // disable elements
                                disableProductElements( $current_single_item_container , $exclusive_product_id );

                                // remove existing selection
                                var $current_selection_item_container = $single_item.find('.ywcp_component_options_selection_container');

                                if( typeof $current_selection_item_container != 'undefined' ) {

                                    var $current_product_id =  $current_selection_item_container.data('selected-id')  ;

                                    if( $current_product_id == $exclusive_product_id )  {
                                        var $clear_button = $current_single_item_container.find('.ywcp_selection_clear');
                                        $clear_button.click();
                                        var $close_button = $current_single_item_container.find('.ywcp_selection_close');
                                        if( $close_button.length > 0 ) {
                                            $close_button.click();
                                        }

                                    }

                                }

                            }

                        }

                    });

                }
            }
        }

    }

    function disableProductElements( $current_single_item_container , $exclusive_product_id ) {
        'use strict';

        var $products_elements = $current_single_item_container.find('.ywcp_product_'+$exclusive_product_id);

        $products_elements.addClass('ywcp_product_disabled');
        $products_elements.attr( 'disabled' , 'disabled' );

    }

    function removeExclusiveSelection( $selected_single_item , $exclusive_product_id ) {
        'use strict';

        if( $selected_single_item.data('exclusive') && $exclusive_product_id > 0 ) {

            var $exclusive_component_key = $selected_single_item.data('component-key');

            // Check all selected products

            var $global_container = $('.ywcp_components_container');

            $ywcp_global_exclusive_selected_ids[$exclusive_component_key] = 0;

            $global_container.find( '.ywcp_components_single_item' ).each( function() {

                var $single_item = $(this);

                var $current_component_key = $single_item.data('component-key');

                if( $current_component_key != $exclusive_component_key ) {

                    var $current_single_item_container =  $single_item.closest('.ywcp_components_single_item');

                    if( typeof $current_single_item_container != 'undefined' ) {

                        // Disable elements
                        enableProductElements( $current_single_item_container , $exclusive_product_id );

                    }

                }

            } );

        }

    }

    function reApplyExclusionList( data ){

        var $product_list_container = $(data);

        for (var key in $ywcp_global_exclusive_selected_ids ) {
            disableProductElements( $product_list_container, $ywcp_global_exclusive_selected_ids[ key ] );
        }

    }

    function enableProductElements( $current_single_item_container , $exclusive_product_id ) {
        'use strict';

        var $products_elements = $current_single_item_container.find('.ywcp_product_'+$exclusive_product_id);

        $products_elements.removeClass('ywcp_product_disabled');
        $products_elements.removeAttr( 'disabled');

    }

    function loadComponentsTotal() {
        'use strict';

        // Calulate total

        var $global_container = $('.ywcp_components_container');
        var $global_qty = parseFloat( $('form.cart.ywcp > div.quantity input.qty').val() );
        var yith_wcp_product_base_price = parseFloat( $('.ywcp_wcp_group_total').data('productprice') ) * $global_qty;
        var $yith_wcp_group_option_total_base_price = $('.yith_wcp_group_option_total_base_price span.amount');
        $yith_wcp_group_option_total_base_price.html( getYwcpFormattedPrice( yith_wcp_product_base_price ) );

        if ( $global_container.length > 0 ) {

            var $yith_wcp_final_total_price = yith_wcp_product_base_price ;
            var $yith_wcp_components_total_price = getComponentsTotal( $global_container, $global_qty );

            if ( $('.ywcp_components_container').data('per-item-price') == 1 ) {
                $yith_wcp_final_total_price += $yith_wcp_components_total_price;
            }
            
            // Add-ons price
            var $yith_wcp_wapo_add_ons_total = $('.yith_wcp_wapo_add_ons_total .amount').html();
            $yith_wcp_wapo_add_ons_total = Number( $yith_wcp_wapo_add_ons_total.replace(/[^0-9]+/g,"") ) / 100;
            if ( $yith_wcp_wapo_add_ons_total > 0 ) {
                $yith_wcp_final_total_price += $yith_wcp_wapo_add_ons_total;
            }

            $(document).trigger( 'yith_wcp_price_updated', [ $yith_wcp_final_total_price ] );

            // Update html

            var $ywcp_components_total_container = $('.yith_wcp_component_total span.amount');

            var $ywcp_wcp_tr_component_total = $( '#ywcp_wcp_tr_component_total' );
            if ( $yith_wcp_components_total_price > 0 ) {
                $ywcp_wcp_tr_component_total.show();
                $ywcp_components_total_container.html( getYwcpFormattedPrice( $yith_wcp_components_total_price ) );
            } else {
                $ywcp_wcp_tr_component_total.hide();
                $ywcp_components_total_container.html( '' );
            }

            var $ywcp_final_total_container = $('.yith_wcp_group_final_total span.amount');

            var $ywcp_wcp_tr_order_total = $( '#ywcp_wcp_tr_order_total' );
            if ( $yith_wcp_final_total_price > 0 ) {
                $ywcp_wcp_tr_order_total.show();
                $ywcp_final_total_container.html( getYwcpFormattedPrice( $yith_wcp_final_total_price ) );
            } else {
                $ywcp_wcp_tr_order_total.hide();
                $ywcp_final_total_container.html( '' );
            }

        }

    }

    function getComponentsTotal( $global_container, $global_qty ) {
        'use strict';

        var $yith_wcp_components_total_price = 0.0;

        $global_container.find( '.ywcp_components_single_item' ).each( function() {

            var $single_item = $(this);
            var $subtotal_container =  $single_item.find('.ywcp_component_subtotal');
            var $subtotal_value = $subtotal_container.find('span.amount');

            var $sold_individually = $single_item.data('sold-individually');
            var $selection_item_container = $single_item.find('.ywcp_component_options_selection_container');

            if ( typeof $selection_item_container != 'undefined' ) {

                var $product_id = parseInt( $selection_item_container.data( 'selected-id' ) );

                if ( $product_id > 0 ) {

                    var $selection_inner_container = $selection_item_container.find('.ywcp_inner_selected_container');

                    var $single_price =  parseFloat( $selection_inner_container.data( 'selected-price' ) );

                    var $single_qty = parseFloat( $selection_inner_container.find('input.qty').val() );

                    var $single_total =  $single_price * $single_qty;

                    if ( ! $sold_individually ) {
                        $single_total *= $global_qty;
                    }

                    if ( $('.ywcp_components_container').data('per-item-price') == 1 ) {
                        $subtotal_value.html( getYwcpFormattedPrice( $single_total ) );
                    } else {
                        $subtotal_value.html( getYwcpFormattedPrice( 0 ) );
                    }
                    $subtotal_container.show();

                    $yith_wcp_components_total_price+= $single_total;

                }

            }

        } );

        return $yith_wcp_components_total_price;

    }

    function showCurrentComponentItem() {
        'use strict';

        var $global_container = $('.ywcp_components_container.ywcp_step');

        if( $global_container.length > 0 ) {

            var $layout_option = $global_container.data('layout-option');

            if( $layout_option == 'step' ) {

                var $components_items = $global_container.find('.ywcp_components_single_item');
                var $navigation_container = $('.ywcp_step_navigation');

                if( $components_items.length > 0 ) {

                    $components_items.removeClass('ywcp_current_step_item');
                    $navigation_container.removeClass('ywcp_step_first');
                    $navigation_container.removeClass('ywcp_step_last');

                    var $ywcp_current_step_index = $ywcp_global_current_step - 1;

                    if( $ywcp_current_step_index >= 0 ) {

                        var $ywcp_current_components_item = $components_items.eq( ( $ywcp_current_step_index ) );

                        $ywcp_current_components_item.addClass('ywcp_current_step_item');

                        if( $ywcp_global_current_step == 1 ) {

                            $navigation_container.addClass('ywcp_step_first');

                        }else if( $components_items.length == $ywcp_global_current_step  ) {

                            $navigation_container.addClass('ywcp_step_last');

                        }

                    }

                    updateStepCurrentInfo( $components_items );

                }

            }

        }

    }

    function nextStepAfterSelection() {
        'use strict';

        var $ywcp_navigation = $( '.ywcp_step_navigation' );

        if( $ywcp_navigation.length > 0 ) {

            if( ! $ywcp_navigation.hasClass( 'ywcp_step_last' ) ) {

                $('.ywcp_step_next').click();

            }

        }

    }

    function updateStepCurrentInfo( $components_items ) {
        'use strict';

        var $ywcp_step_current_info = $( '.ywcp_step_current_info' );

        if( $ywcp_step_current_info.length > 0 ) {

            $ywcp_step_current_info.html(( $ywcp_global_current_step.toString() )+'/'+$components_items.length.toString());
        }

    }

    // Dependence

    function getDependenciesData() {
        'use strict';

        var $global_container = $('.ywcp_components_container');
        var dependencies_data = $global_container.data( 'dependencies' );

        var dependencies_data_array =[];

        // create master index
        for( var i in dependencies_data ) {
            if (dependencies_data.hasOwnProperty(i)){
                var master_key = i.split('_')[0];
                dependencies_data_array[master_key] = [];
            }
        }

        // add single array to each dependence
        for( var i in dependencies_data ) {
            if (dependencies_data.hasOwnProperty(i)){
                var master_key = i.split('_')[0];
                var component_key = i.split('_')[1];
                dependencies_data_array[master_key][component_key] = dependencies_data[i];
            }
        }

        return dependencies_data_array;

    }

    function applyDependenciesData() {
        'use strict';

        for( var $dependence_key in $ywcp_dependencied_data ) {

            var $selection_matched = true;

            if ( $ywcp_dependencied_data.hasOwnProperty( $dependence_key ) ) {

                for( var $component_key in $ywcp_dependencied_data[ $dependence_key ] ) {

                    $selection_matched = checkSingleDependence( $dependence_key , $component_key , $ywcp_dependencied_data[ $dependence_key ][ $component_key ] );

                    if( ! $selection_matched ) {

                        // flush previous actions

                        // HIDED COMPONENT

                        flush_components_hided( $dependence_key );


                        // SELECTION IS

                        flush_selected_components( $dependence_key );

                        // SELECTION IS NOT

                        flush_disabled_values( $dependence_key );

                        break;
                    }
                }

            }

        }

    }

    function checkSingleDependence( $dependence_key , $component_key , $single_dependence ) {
        'use strict';

        switch ( $single_dependence.action_type ) {

            case 'do':

                doDependenceAction( $dependence_key , $component_key , $single_dependence )

                return true;

                break;

            case 'if':

                return isMatchConditionalDependence( $component_key , $single_dependence );

                break;

        }

        return false;
    }

    function isMatchConditionalDependence( $component_key , $single_dependence ) {
        'use strict';

        var $is_match = true;

        var $single_component_item = $( '#ywcp_component_'+ $component_key.toString() )

        if( $single_component_item.length > 0 ) {

            var $selected_value = getComponentSelectedValue( $single_component_item );

            switch ( $single_dependence.selection_type ) {

                case 'not_selected':

                    $is_match = ( $selected_value === 0 );
                    break;

                case 'is_selected':

                    $is_match = ( $selected_value > 0 );

                    break;

                case 'selection_is':

                    $is_match = checkSelectionIsMatch( $selected_value , $single_component_item , $single_dependence , 0 );

                    break;

                case 'selection_is_not':

                    $is_match = checkSelectionIsMatch( $selected_value , $single_component_item , $single_dependence , -1 );

                    break;

            }

        }

        return $is_match;

    }

    function checkSelectionIsMatch( $selected_value , $single_component_item , $single_dependence , $conditionValue ){
        'use strict';

        if( $selected_value > 0 ) {

            // check for all variation
            var $master_selected_value = getMasterVariableComponentSelectedValue( $single_component_item );

            // variable product
            if ( $selected_value != $master_selected_value ) {

                return ( $.inArray( $master_selected_value.toString(), $single_dependence.option_ids ) === $conditionValue ) || ( $.inArray( $selected_value.toString(), $single_dependence.option_ids ) === $conditionValue );

            } else {  // simple product

                return ( $.inArray( $selected_value.toString(), $single_dependence.option_ids ) === $conditionValue );
            }

        } else {

            return false;

        }

    }

    function doDependenceAction( $dependence_key, $component_key , $single_dependence ){
        'use strict';

        var $single_component_item = $( '#ywcp_component_'+ $component_key.toString() );

        switch ( $single_dependence.do_type ) {

            case 'hided':

                $single_component_item.addClass( 'ywcp_components_single_item_depenence_'+$dependence_key );

                $single_component_item.addClass( 'ywcp_components_single_item_depenence_hided' );

                // remove eventually selection

                clearSelection( $single_component_item );

                break;

            case 'selection_is':

                if ( $single_dependence.option_ids ) {

                    if ( $single_dependence.option_ids.length > 0 ) {

                        $single_component_item.addClass( 'ywcp_components_single_item_dependence_selected_'+$dependence_key );

                        $single_component_item.addClass( 'ywcp_components_single_item_dependence_selected' );

                        setSelection( $single_dependence.option_ids[0], $single_component_item , false , false , false );

                    }

                }

                break;

            case 'selection_is_not':

                if( typeof $ywcp_global_dependece_selected_disabled_ids[$dependence_key] == 'undefined') {
                    $ywcp_global_dependece_selected_disabled_ids[$dependence_key] = [];
                }

                $ywcp_global_dependece_selected_disabled_ids[$dependence_key][$component_key] =  $single_dependence.option_ids;

                if( $single_dependence.option_ids.length > 0 ) {

                    var $selected_value = getComponentSelectedValue( $single_component_item );

                    for( var i = 0; i < $single_dependence.option_ids.length; i++ ) {

                        if( $selected_value == $single_dependence.option_ids[i] ) {
                            clearSelection( $single_component_item );
                        }

                        disableProductElements( $single_component_item , $single_dependence.option_ids[i] )

                    }

                }

                break;
        }

    }

    function flush_components_hided( $dependence_key ) {

        var $marked_component_items = $('.ywcp_components_single_item_depenence_'+$dependence_key);

        if( $marked_component_items.length > 0 ) {

            //hided

            $marked_component_items.removeClass('ywcp_components_single_item_depenence_hided');

            $marked_component_items.removeClass('.ywcp_components_single_item_depenence_'+$dependence_key);


        }

    }

    function flush_selected_components( $dependence_key ) {

        var $dependence_class_name = 'ywcp_components_single_item_dependence_selected_'+$dependence_key
        var $selected_component = $('.'+$dependence_class_name);

        if( $selected_component.length > 0 ) {

            $selected_component.each(function () {

                clearSelection( $(this) );

                $(this).removeClass( $dependence_class_name );
                $(this).removeClass( 'ywcp_components_single_item_dependence_selected' );

            });

        }

    }

    function flush_disabled_values( $dependence_key ) {

        if( typeof $ywcp_global_dependece_selected_disabled_ids[ $dependence_key ] != 'undefined' ) {

            var $dependeces_disabled_list = $ywcp_global_dependece_selected_disabled_ids[ $dependence_key ];

            for ( var $component_key in $dependeces_disabled_list ) {

                if ( $dependeces_disabled_list.hasOwnProperty( $component_key ) ) {

                    var $disableds_ids = $dependeces_disabled_list[$component_key];

                    if( typeof $disableds_ids != 'undefined' ) {

                        var $single_component_item = $( '#ywcp_component_'+$component_key.toString() );

                        for( var i = 0; i < $disableds_ids.length; i++ ) {

                            enableProductElements( $single_component_item , $disableds_ids[i] )

                        }

                        $dependeces_disabled_list[$component_key] = [];

                    }

                }

            }

        }

    }

    // End Dependence

    /* Re Edit Cart */

    function checkReEditCartValues() {

        var $global_container = $('.ywcp_components_container');

        $global_container.find( '.ywcp_components_single_item' ).each( function() {

            var $wcp_key = $(this).data('component-key');

            var $red_edit_cart_variation =  $( '.ywcp_edit_cart_item_variation_' + $wcp_key.toString() );

            var $red_edit_cart_parent =  $( '.ywcp_edit_cart_item_parent_' + $wcp_key.toString() );

            if( $red_edit_cart_variation.length > 0 && $red_edit_cart_variation.val() != '' ) {

                setSelection( $red_edit_cart_variation.val(), $(this), false, false, false );

            } else if( $red_edit_cart_parent.length > 0 ) {

                setSelection( $red_edit_cart_parent.val(), $(this), false, false, false );

            }

        } );

    }

    /**
     *
     * @param price
     * @author Andrea Frascaspata
     * @returns {*}
     */
    function getYwcpFormattedPrice( price ) {
        'use strict';

        var formatted_price = price;

        if( typeof accounting != 'undefined' ) {

            formatted_price = accounting.formatMoney( price , {
                symbol      : yith_wcp_general.currency_format_symbol,
                decimal     : yith_wcp_general.currency_format_decimal_sep,
                thousand    : yith_wcp_general.currency_format_thousand_sep,
                precision   : yith_wcp_general.currency_format_num_decimals,
                format      : yith_wcp_general.currency_format
            } );

        }

        return formatted_price;
    }

});