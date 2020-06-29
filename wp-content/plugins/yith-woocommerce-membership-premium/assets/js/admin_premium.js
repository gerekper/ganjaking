jQuery( function ( $ ) {
    $( '.tips' ).tipTip( {
                             'attribute': 'data-tip',
                             'fadeIn'   : 50,
                             'fadeOut'  : 50,
                             'delay'    : 0
                         } );

    $( '.tips-top' ).tipTip( {
                                 'attribute'      : 'data-tip',
                                 'fadeIn'         : 50,
                                 'fadeOut'        : 50,
                                 'delay'          : 0,
                                 'defaultPosition': 'top'
                             } );

    $( '.yith-wcmbs-tabs' ).tabs();

    $( '.yith-wcmbs-select2' ).select2();
    $( '.yith-wcmbs-color-picker' ).wpColorPicker();

    $( '.yith-wcmbs-date' ).datepicker( {
                                            dateFormat: 'yy-mm-dd'
                                        } );

    var delay_container                      = $( '#yith-wcmbs-delay-time-for-plans-container' ),
        hide_content_select                  = $( '#yith-wcmbs-hide-contents' ),
        redirect_link_container              = $( '#yith-wcmbs-redirect-link' ).closest( 'tr' ),
        products_in_membership_select        = $( '#yith-wcmbs-products-in-membership-management' ),
        download_link_position_container     = $( '#yith-wcmbs-download-link-position' ).closest( 'tr' ),
        hide_price_and_add_to_cart_container = $( '#yith-wcmbs-hide-price-and-add-to-cart' ).closest( 'tr' ),
        control_active_delay                 = function () {
            var delay_input = $( '.yith-wcmbs-delay-number-input' );

            if ( delay_input.length < 1 ) {
                delay_container.hide();
            } else {
                delay_container.show();
            }
        }
        ;


    var restrict_access_plan_chosen = $( '#yith_wcmbs_restrict_access_plan' );

    restrict_access_plan_chosen.on( 'change', function () {
        var rows             = delay_container.find( 'tr.yith-wcmbs-delay-row' ),
            selected_plans   = $( this ).val(),
            current_rows_ids = [];


        rows.each( function () {
            var row_plan_id = $( this ).data( 'plan-id' ) + '';
            current_rows_ids.push( row_plan_id );
            if ( selected_plans == null || selected_plans.indexOf( row_plan_id ) < 0 ) {
                $( this ).remove();
            }
        } );

        for ( var i in selected_plans ) {
            if ( current_rows_ids.length == 0 || ( current_rows_ids.length > 0 && current_rows_ids.indexOf( selected_plans[ i ] ) < 0 ) ) {
                var plan_id     = selected_plans[ i ],
                    plan_name   = restrict_access_plan_chosen.find( 'option[value="' + plan_id + '"]' ).html(),
                    html_to_add = '<tr class="yith-wcmbs-delay-row" data-plan-id="' + plan_id + '">' +
                                  '<td><label for="yith-wcmbs-delay-' + plan_id + '">' + plan_name + '</label></td>' +
                                  '<td><input class="yith-wcmbs-delay-number-input" data-plan-id="' + plan_id + '" id="yith-wcmbs-delay-' + plan_id + '" name="_yith_wcmbs_plan_delay[' + plan_id + ']" type="number" value="0" min="0"></td>' +
                                  '</tr>';

                delay_container.append( html_to_add );
            }
        }

        control_active_delay();
    } );

    control_active_delay();

    hide_content_select.on( 'change', function () {
        if ( $( this )[ 0 ].selectedIndex == 2 ) {
            redirect_link_container.show();
        } else {
            redirect_link_container.hide();
        }
    } );
    hide_content_select.trigger( 'change' );

    products_in_membership_select.on( 'change', function () {
        if ( $( this )[ 0 ].selectedIndex == 1 ) {
            download_link_position_container.show();
            hide_price_and_add_to_cart_container.show();
        } else {
            download_link_position_container.hide();
            hide_price_and_add_to_cart_container.hide();
        }
    } );
    products_in_membership_select.trigger( 'change' );


    /* - - - - - - - - - -  Plan Item Order - - - - - - - - - - - */

    var plan_item_order_container = $( '#yith-wcmbs-plan-item-order-container' ),
        items                     = plan_item_order_container.find( 'li' ).get(),
        plan_item_text            = $( '#yith-wcmbs-plan-item-text' ),
        block_params              = {
            message        : null,
            overlayCSS     : {
                background: '#000',
                opacity   : 0.6
            },
            ignoreIfBlocked: true
        };

    items.sort( function ( a, b ) {
        var compA = parseInt( $( a ).attr( 'rel' ) );
        var compB = parseInt( $( b ).attr( 'rel' ) );
        return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
    } );

    $( items ).each( function ( idx, itm ) {
        plan_item_order_container.append( itm );
    } );
    //ordering
    plan_item_order_container.sortable( {
                                            items               : 'li',
                                            cursor              : 'move',
                                            scrollSensitivity   : 40,
                                            forcePlaceholderSize: true,
                                            helper              : 'clone',
                                            opacity             : 0.80,
                                            revert              : true,
                                            stop                : function ( event, ui ) {
                                                if ( ui.item.hasClass( 'yith-wcmbs-plan-item-text' ) ) {
                                                    ui.item.find( 'input' ).focus();
                                                }
                                            }
                                        } );

    plan_item_text.draggable( {
                                  connectToSortable: '#yith-wcmbs-plan-item-order-container',
                                  helper           : "clone",
                                  revert           : "invalid",
                                  stop             : function ( event, ui ) {
                                      ui.helper.html( '<input type="text" name="_yith_wcmbs_plan_items[]" /><span class="dashicons dashicons-no-alt close"></span>' );
                                      ui.helper.css( { height: 'auto' } );
                                  }
                              } );

    plan_item_order_container.on( 'click', '.close', function ( event ) {
        $( event.target ).closest( 'li' ).remove();
    } );

    plan_item_order_container.find( 'li' ).disableSelection();
    plan_item_text.disableSelection();


    // Delete from plan Actions
    plan_item_order_container.on( 'click', '.yith-wcmbs-delete-from-plan', function ( e ) {
        var target  = $( e.target ),
            li      = target.closest( 'li' ),
            post_id = target.data( 'post-id' ),
            plan_id = target.data( 'plan-id' );

        li.block( block_params );

        // send data for ajax request
        $.ajax( {
                    url    : ajaxurl,
                    type   : 'POST',
                    data   : {
                        action : 'yith_wcmbs_remove_plan_for_post',
                        post_id: post_id,
                        plan_id: plan_id,
                    },
                    success: function ( data ) {
                        console.log( data );
                        li.unblock();
                        li.remove();
                    }
                } );
    } );

    plan_item_order_container.on( 'click', '.yith-wcmbs-hide-show-item', function ( e ) {
        var target         = $( e.target ),
            li             = target.closest( 'li' ),
            hidden_item_id = li.children( 'input.yith_wcmbs_hidden_item_ids' ),
            post_id        = target.data( 'post-id' ),
            item_action    = 'show';

        if ( target.is( '.dashicons-visibility' ) ) {
            item_action = 'hide';
        }

        if ( item_action == 'show' ) {
            hidden_item_id.prop( 'disabled', true );
            target.removeClass( 'dashicons-hidden' ).addClass( 'dashicons-visibility' );
        } else {
            hidden_item_id.prop( 'disabled', false );
            target.removeClass( 'dashicons-visibility' ).addClass( 'dashicons-hidden' );
        }
    } );


    /* - - - - - - - - - -  Plan Item Order - - - - - - - - - - - */
    /* - - - - - - - - - - - - - END - - -  - - - - - - - - - - - */


    /* - - - - - - - - - -  BULK EDIT - - - - - - - - - - - */
    $( '#bulk_edit' ).on( 'click', function () {
        // define the bulk edit row
        var $bulk_row = $( '#bulk-edit' );

        // get the selected post ids that are being edited
        var $post_ids = [];
        $bulk_row.find( '#bulk-titles' ).children().each( function () {
            $post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
        } );

        // get the data
        var $plans_checkboxes = $( '.plans-checklist', $bulk_row ).find( 'input:checked' );
        var plans_ids         = [];
        $plans_checkboxes.each( function () {
            plans_ids.push( $( this ).val() );
        } );

        // save the data
        $.ajax( {
                    url  : ajaxurl,
                    type : 'POST',
                    async: false,
                    cache: false,
                    data : {
                        action                         : 'yith_wcmbs_save_bulk_edit',
                        post_ids                       : $post_ids,
                        yith_wcmbs_restrict_access_plan: plans_ids
                    }
                } );
    } );

    $( document ).on( 'yith_wcmbs_select2_init', function () {
        var ajax_select2 = $( '.yith_wcmbs_ajax_select2_select_customer' );
        ajax_select2.each( function () {
            var select2_args = {
                allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                placeholder       : $( this ).data( 'placeholder' ),
                minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup      : function ( m ) {
                    return m;
                },
                ajax              : {
                    url           : ajaxurl,
                    dataType      : 'json',
                    quietMillis   : 250,
                    data          : function ( params ) {
                        return {
                            term                         : params.term,
                            action                       : 'woocommerce_json_search_customers',
                            security                     : obj.customer_nonce,
                            yith_wcmbs_show_username_only: true
                        };
                    },
                    processResults: function ( data ) {
                        var terms = [];
                        if ( data ) {
                            $.each( data, function ( id, text ) {
                                terms.push( { id: id, text: text } );
                            } );
                        }
                        return {
                            results: terms
                        };
                    },
                    cache         : true
                }
            };

            $( this ).select2( select2_args );
        } );

        ajax_select2.on( 'yith_wcmbs_select2_reset', function () {
            $( this ).val( '' ).trigger( 'change' );

        } ).trigger( 'yith_wcmbs_select2_reset' );
    } ).trigger( 'yith_wcmbs_select2_init' );

    /**
     * CHOSEN SELECT and DESELECT ALL BUTTON
     */

    $( '.yith-wcmbs-select2-select-all' ).on( 'click', function ( e ) {
        var target         = $( e.target ),
            container_id   = target.data( 'container-id' ),
            container      = $( '#' + container_id ),
            current_select = container.find( '.yith-wcmbs-select2' ).first();

        current_select.find( 'option' ).prop( 'selected', true );
        current_select.trigger( 'change' );
    } );

    $( '.yith-wcmbs-select2-deselect-all' ).on( 'click', function ( e ) {
        var target         = $( e.target ),
            container_id   = target.data( 'container-id' ),
            container      = $( '#' + container_id ),
            current_select = container.find( '.yith-wcmbs-select2' ).first();

        current_select.find( 'option:selected' ).removeAttr( 'selected' );
        current_select.trigger( 'change' );
    } );


    // Copy on clipboard
    $( document ).on( 'click', '.yith-wcmbs-copy-to-clipboard', function ( event ) {
        var target           = $( this ),
            selector_to_copy = target.data( 'selector-to-copy' ),
            obj_to_copy      = $( selector_to_copy );

        if ( obj_to_copy.length > 0 ) {
            var temp = $( "<input>" );
            $( 'body' ).append( temp );

            temp.val( obj_to_copy.html() ).select();
            document.execCommand( "copy" );

            temp.remove();
        }
    } );

} );