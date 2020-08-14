/**
 * Administration
 *
 * @author Your Inspiration Themes
 * @package YITH Composite Products for WooCommerce
 * @version 1.0.0
 */
jQuery(document).ready( function($) {
    'use strict';

    var wc_cp_block_params = {};

    wc_cp_block_params = {
        message:    null,
        overlayCSS: {
            background: '#fff',
            opacity:    0.6
        }
    };

    // Composite type specific options.
    $( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val, select ) {
        'use strict';

        if ( select_val === 'yith-composite' ) {

            $( 'input#_downloadable' ).prop( 'checked', false );
            $( 'input#_virtual' ).removeAttr( 'checked' );

            $( '.show_if_simple' ).not( '._sold_individually_field' ).show();
            $( '.show_if_external' ).hide();

            $( 'input#_downloadable' ).closest( '.show_if_simple' ).hide();
            $( 'input#_virtual' ).closest( '.show_if_simple' ).hide();

            $( '.wc-tabs > li.general_options.general_tab' ).show();
            $( '.pricing' ).show();
            $( 'ul.product_data_tabs li:visible' ).eq(0).find('a').click();

            $( 'input#_manage_stock' ).change();
            $( 'li.inventory_tab' ).show();
            $( 'input#_ywcp_options_product_per_item_shipping' ).change();
            $( 'input#_ywcp_options_product_per_item_pricing' ).change();

            $('.options_group.pricing').show();
            $('.options_group ._tax_status_field').parent().show();

        }

    } );

    // Non-bundled shipping.
    $( 'input#_ywcp_options_product_per_item_shipping' ).change( function() {
        'use strict';

        if ( $( 'select#product-type' ).val() === 'yith-composite' ) {

            if ( $( 'input#_ywcp_options_product_per_item_shipping' ).is( ':checked' ) ) {

                $( '.show_if_virtual' ).show();
                $( '.hide_if_virtual' ).hide();

                if ( $( '.shipping_tab' ).hasClass( 'active' ) ) {
                    $( 'ul.product_data_tabs li:visible' ).eq(0).find('a').click();
                }

            } else {

                $( '.show_if_virtual' ).hide();
                $( '.hide_if_virtual' ).show();
            }
        }

    } ).change();

    // Non-bundled shipping.
    $( 'input#_ywcp_downloadable' ).change( function() {
        'use strict';

        if ( $( 'select#product-type' ).val() === 'yith-composite' ) {

            if ( $( 'input#_ywcp_downloadable' ).is( ':checked' ) ) {

                $( '.show_if_downloadable' ).show();
                $( '.hide_if_downloadable' ).hide();

            } else {

                $( '.show_if_downloadable' ).hide();
                $( '.hide_if_downloadable' ).show();
            }
        }

    } ).change();

    // Save components list

    $( '.ywcp_save_components' ).on( 'click', function() {
        'use strict';

        var data = {
            post_id: 		woocommerce_admin_meta_boxes.post_id,
            data:			$( '#ywcp_tab_component' ).find( 'input, select, textarea' ).serialize(),
            action: 		'ywcp_components_save'
        };

        $( '#ywcp_tab_component' ).block( wc_cp_block_params );

        $.post( woocommerce_admin_meta_boxes.ajax_url, data, function( post_response ) {

            var this_page = window.location.toString();

            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&' );

            $( 'body' ).trigger( 'ywcp_woocommerce_components_saved' );

            $.get( this_page, function( response ) {

                $( '#ywcp_tab_component' ).unblock();

            } );

        }, 'json' );

    } );

    $( 'body' ).on( 'ywcp_woocommerce_components_saved',  function() {
        'use strict';

        ywcp_load_component_list();

        $( '.ywcp_save_dependencies' ).trigger('click');

    } );

    // Save dependencies on options via ajax.

    $( '.ywcp_save_dependencies' ).on( 'click', function() {
        'use strict';

        var data = {
            post_id: 		woocommerce_admin_meta_boxes.post_id,
            data:			$( '#ywcp_tab_dependecies' ).find( 'input, select, textarea' ).serialize(),
            action: 		'ywcp_dependencies_save'
        };

        $( '#ywcp_tab_dependecies' ).block( wc_cp_block_params );

        $.post( woocommerce_admin_meta_boxes.ajax_url, data, function( post_response ) {

            var this_page = window.location.toString();

            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&' );

            $( 'body' ).trigger( 'ywcp_woocommerce_dependencies_saved' );

            $.get( this_page, function( response ) {

                $( '#ywcp_tab_dependecies' ).unblock();

            } );

        }, 'json' );

    } );

    $( 'body' ).on( 'ywcp_woocommerce_dependencies_saved',  function() {
        'use strict';

        ywcp_load_dependencies_list();

    } );

    // add component group

    $( 'body' ).on( 'click', 'button.ywcp_add_component', function() {
        'use strict';

        $( '#ywcp_tab_component' ).block( wc_cp_block_params );

        var data = {
            post_id: 	woocommerce_admin_meta_boxes.post_id,
            action: 	'ywcp_ajax_add_component',
            ywcp_component_index: ywcp_generateGuid()
        };

        var $ywcp_container = $( '#ywcp_components_list_container_items' );

        $.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

            $ywcp_container.append( response );

            var added = $( '#ywcp_components_list_container_items .ywcp_components_list_container_single_item' ).last();

            handle_sorting_component_list();

            handle_wc_toltip( added );

            handle_selection_type();

            handle_product_search();

            handle_elements_select2( added.find( '.categories_id-select2' ) );

            added.trigger( 'ywcp_woocommerce_component_added' );

            $( '#ywcp_tab_component' ).unblock();

        } );

        return false;

    } );

    $( 'body' ).on( 'click', 'button.ywcp_add_dependencies', function() {
        'use strict';

        $( '#ywcp_tab_dependecies' ).block( wc_cp_block_params );

        var data = {
            post_id: 	woocommerce_admin_meta_boxes.post_id,
            action: 	'ywcp_ajax_add_dependence',
            ywcp_dependence_index: ywcp_generateGuid()
        };

        var $ywcp_container = $( '#ywcp_dependencies_list_container_items' );

        $.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

            $ywcp_container.append( response );

            var added = $( '#ywcp_dependencies_list_container_items .ywcp_dependencieslist_container_single_item' ).last();

            handle_elements_select2_single_selection( added.find( '.ywcp_dependence_selection_product_id-select2' ) );

            handle_selection_dependencies_type();

            handle_action_dependencies_type();

            added.trigger( 'ywcp_woocommerce_dependencies_added' );

            $( '#ywcp_tab_dependecies' ).unblock();


        } );

        return false;

    } );

    $( 'body' ).on( 'click', 'button.ywcp_remove_component', function() {
        'use strict';

        var $parent = $( this ).parent().parent();

        $parent.find('*').off();
        $parent.remove();

    } );

    $( 'body' ).on( 'click', 'button.ywcp_remove_dependence', function() {
        'use strict';

        var $parent = $( this ).parent().parent();

        $parent.find('*').off();
        $parent.remove();

    } );

    handle_sorting_component_list();

    handle_sorting_dependencies_list();

    handle_selection_type();

    handle_selection_dependencies_type();

    handle_action_dependencies_type();

    handle_product_search();

    handle_elements_select2();

    handle_elements_select2_single_selection();

    // handle function declaration

    function handle_sorting_component_list() {
        'use strict';

        // SORTABLE
        $('#ywcp_components_list_container_items.sortable').sortable({
                axis  : 'y',
                update: function (event, ui) {

                }
            }) ;

        $( document ).on('click', '.ywcp_components_list_container .ywcp_expand_all', function () {
            $(this).closest('.ywcp_list_container').find('.ywcp_list_container_single_item').removeClass('closed').addClass('open');
            return false;
        });

        $( document ).on('click', '.ywcp_components_list_container .ywcp_close_all', function () {
            $(this).closest('.ywcp_list_container').find('.ywcp_list_container_single_item').removeClass('open').addClass('closed');
                return false;
            });


    }

    function handle_sorting_dependencies_list() {
        'use strict';

        // SORTABLE
        $('#ywcp_dependencies_list_container_items.sortable').sortable({
            axis  : 'y',
            update: function (event, ui) {

            }
        }) ;

        $( document ).on('click', '.ywcp_dependencies_list_container .ywcp_expand_all', function () {
            $(this).closest('.ywcp_list_container').find('.ywcp_dependencieslist_container_single_item').removeClass('closed').addClass('open');
            return false;
        });

        $( document ).on('click', '.ywcp_dependencies_list_container .ywcp_close_all', function () {
            $(this).closest('.ywcp_list_container').find('.ywcp_dependencieslist_container_single_item').removeClass('open').addClass('closed');
            return false;
        });


    }

    function handle_wc_toltip( object ) {
        'use strict';

        object.find( '.woocommerce-help-tip' ).tipTip( {
            'attribute' : 'data-tip',
            'fadeIn' : 50,
            'fadeOut' : 50,
            'delay' : 200
        } );
    }

    function handle_product_search() {
        'use strict';

        $( document.body ).trigger('wc-enhanced-select-init');

    }

    function handle_selection_type() {
        'use strict';

        $( 'body' ).on( 'change', 'select.ywcp-product-search', function( ) {

            var select_val = $( this ).val();

            var $container = $( this ).closest('.ywcp_components_list_container_single_item_form');

            $container.find('.ywcp_layout_options_container').hide();

            if ( select_val === 'product' ) {

                $container.find('.ywcp-product-search-container').show();

            } else if( select_val === 'product_categories' ) {

                $container.find('.ywcp-categories-search-container').show();

            } else if( select_val === 'product_tags' ) {

                $container.find('.ywcp-tags-search-container').show();

            }

        } );

        $('select.ywcp-product-search').change();

    }

    function handle_selection_dependencies_type() {
        'use strict';

        $( 'body' ).on( 'change', 'select.ywcp-dependecies-selection-option, select.ywcp-dependecies-do-option', function( ) {

            var select_val = $( this ).val();

            var $container = $( this ).closest('.ywcp_dependencieslist_component_single_item');

            var $container_item =  $container.find('.ywcp_dependencieslist_component_single_item_products_chosen');

            if ( select_val === 'selection_is' || select_val === 'selection_is_not' ) {

                $container_item.show();

            } else {

                $container_item.hide();

            }

        } );

        $('select.ywcp-dependecies-selection-option, select.ywcp-dependecies-do-option').change();

    }

    function handle_action_dependencies_type() {
        'use strict';

        $( 'body' ).on( 'change', 'select.ywcp-dependecies-action-option', function( ) {

            var select_val = $( this ).val();

            var $container = $( this ).closest('.ywcp_dependencieslist_component_single_item');

            var $dropdown_item_action = $container.find('.ywcp_dependecies_action_container');

            var $dropdown_item_condition = $container.find('.ywcp_dependecies_condition_container');

            if ( select_val === 'do' ) {

                $dropdown_item_action.show();
                $dropdown_item_action.find('select').change();
                $dropdown_item_condition.hide();

            } else if ( select_val === 'if' ) {

                $dropdown_item_action.hide();
                $dropdown_item_condition.show();
                $dropdown_item_condition.find('select').change();

            }


        } );

        $('select.ywcp-dependecies-action-option').change();

    }

    function handle_elements_select2( $object ){

       var select2_args = $.extend({
            placeholder: $( this ).data( 'placeholder' ),
            width: '50%',
        }, ywcp_getEnhancedSelectFormatString() );

        if( typeof $object == 'undefined') {
            $('.categories_id-select2, .product_id-select2').select2( select2_args );
        } else {
            $object.select2( select2_args );
        }


    }

    function handle_elements_select2_single_selection( $object ){

        var select2_args = $.extend({
            placeholder: $( this ).data( 'placeholder' ),
            width: '50%',
            maximumSelectionLength: 1,
            maximumSelectionSize: 1
        }, ywcp_getEnhancedSelectFormatString() );

        if( typeof $object == 'undefined') {
            $('.ywcp_dependence_selection_product_id-select2').select2( select2_args );
        } else {
            $object.select2( select2_args );
        }

    }

    // end handle events

    function ywcp_getEnhancedSelectFormatString() {
        var formatString = {
            noResults: function() {
                return wc_enhanced_select_params.i18n_no_matches;
            },
            errorLoading: function() {
                return wc_enhanced_select_params.i18n_ajax_error;
            },
            inputTooShort: function( args ) {
                var remainingChars = args.minimum - args.input.length;

                if ( 1 === remainingChars ) {
                    return wc_enhanced_select_params.i18n_input_too_short_1;
                }

                return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
            },
            inputTooLong: function( args ) {
                var overChars = args.input.length - args.maximum;

                if ( 1 === overChars ) {
                    return wc_enhanced_select_params.i18n_input_too_long_1;
                }

                return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
            },
            maximumSelected: function( args ) {
                if ( args.maximum === 1 ) {
                    return wc_enhanced_select_params.i18n_selection_too_long_1;
                }

                return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
            },
            loadingMore: function() {
                return wc_enhanced_select_params.i18n_load_more;
            },
            searching: function() {
                return wc_enhanced_select_params.i18n_searching;
            }
        };

        var language = { 'language' : formatString };

        return language;
    }

    function ywcp_generateGuid() {
        var result, i, j;
        result = '';
        for(j=0; j<32; j++) {
            if( j == 8 || j == 12|| j == 16|| j == 20)
                result = result + '-';
            i = Math.floor(Math.random()*16).toString(16).toUpperCase();
            result = result + i;
        }
        return result;
    }

    function ywcp_load_component_list() {

        var data = {
            post_id: 		woocommerce_admin_meta_boxes.post_id,
            action: 		'ywcp_ajax_load_components'
        };

        var $ywcp_container = $( '#ywcp_components_list_container_items' );

        $( '#ywcp_tab_component' ).block( wc_cp_block_params );

        $.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

            $ywcp_container.html( response );

            handle_sorting_component_list();

            handle_wc_toltip( $ywcp_container );

            handle_selection_type();

            handle_product_search();

            handle_elements_select2( $ywcp_container.find( '.categories_id-select2' ) );

            $ywcp_container.trigger( 'ywcp_woocommerce_components_loaded' );

            $( '#ywcp_tab_component' ).unblock();

        } );

    }

    function ywcp_load_dependencies_list() {

        var data = {
            post_id: 		woocommerce_admin_meta_boxes.post_id,
            action: 		'ywcp_ajax_load_dependencies'
        };

        var $ywcp_container = $( '#ywcp_dependencies_list_container_items' );

        $( '#ywcp_tab_dependecies' ).block( wc_cp_block_params );

        $.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

            $ywcp_container.html( response );

            handle_sorting_dependencies_list();

            handle_elements_select2_single_selection( $ywcp_container.find( '.ywcp_dependence_selection_product_id-select2' ) );

            handle_selection_dependencies_type();

            handle_action_dependencies_type();

            $ywcp_container.trigger( 'ywcp_woocommerce_dependencies_loaded' );

            $( '#ywcp_tab_dependecies' ).unblock();

        } );

    }

});