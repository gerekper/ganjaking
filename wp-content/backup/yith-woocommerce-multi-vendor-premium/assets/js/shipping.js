(function ($) {

    if ( typeof yith_wpv_shipping_general === 'undefined' )
        return false;

    var wc_cp_block_params = {};

    wc_cp_block_params = {
        message:    null,
        overlayCSS: {
            background: '#fff',
            opacity:    0.6
        }
    };

    //shipping

    $(document).on( 'click' , '#yith-wcmv-btn-add' , function(e){
        'use strict';

        e.preventDefault();

    });

    $(document).on( 'click' , '.yith-wpdv-wc-shipping-zone-postcodes-toggle' , function(e){
        'use strict';

        e.preventDefault();
        $(this).closest('tr').find( '.wc-shipping-zone-postcodes' ).show();
        $(this).hide();

    }) ;

    $(document).on( 'click' , '.yith-wpdv-wc-shipping-zone-delete' , function(e){
        'use strict';

        e.preventDefault();
        $(this).closest('tr').remove();

    }) ;

    //areas
    $('#yith-wpv-shipping-metohd-btn-add').click( function(e){
        'use strict';

        e.preventDefault();

        var $table =  $(document).find('table.wc-shipping-zones');
        var $tbody = $table.children('tbody');

        $table.block( wc_cp_block_params );

        var data = {
            action: 'yith_wpv_shipping_add_new_option',
        };

        $.post( yith_wpv_shipping_general.ajax_url, data, function ( response ) {

            $tbody.append( response );

            $tbody.closest('form').find('select[name="type"]').change();

            yith_wpv_init_components();

            $table.unblock();

        } );


    });

    yith_wpv_init_components();

    function yith_wpv_init_components() {
        'use strict';

        $('.wc-shipping-zone-rows').sortable({
            items: 'tr',
            cursor: 'move',
            axis: 'y',
            handle: 'td.wc-shipping-zone-sort',
            scrollSensitivity: 40
        });


        $( '.wc-shipping-zone-region-select:not(.enhanced)' ).select2();
        $( '.wc-shipping-zone-region-select:not(.enhanced)' ).addClass('enhanced');

        var tiptip_args = {
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200
        };

        $( '.tips, .help_tip, .woocommerce-help-tip' ).tipTip( tiptip_args );
    }

    //end areas

    // Modal Shipping Methods

    $( document.body ).on( 'click' , '.yith-wpv-shipping-methods-add' , function(e){
        'use strict';

        e.preventDefault();

        var $key = $(this).data('key');

        $( this ).WCBackboneModal({
            template : 'wc-modal-add-shipping-method',
            variable : {
                yith_wpd_area_key : $key
            }
        });

    });

    $( document.body ).on( 'wc_backbone_modal_loaded', function(e,data,formData) {
        'use strict';

        if( typeof $('#yith-wpv-shipping-method-dropdown') != 'undefined' ) {
            $('#yith-wpv-shipping-method-dropdown').change();
        }

        // load shipping methoid

        var $modal_edit_method = $('.wc-backbone-modal-edit-shipping-mode');

        if($modal_edit_method.length > 0) {

            var $yith_wpd_shipping_type = $modal_edit_method.find('input[name="yith_wpd_shipping_type"]').val();
            var $yith_wpd_shipping_key = $modal_edit_method.find('input[name="yith_wpd_shipping_key"]').val();

            // current data values ------------------------------------------------------------------------------------------------------

            var input_title = $('#method_title_' + $yith_wpd_shipping_key );
            var input_tax_status = $('#method_tax_status_' + $yith_wpd_shipping_key );
            var input_method_cost = $('#method_cost_' + $yith_wpd_shipping_key );

            var input_method_requires = $('#method_requires_' + $yith_wpd_shipping_key );
            var input_min_amount = $('#min_amount_' + $yith_wpd_shipping_key );

            //-----------------------------------------------------------------------------------------------------------------------------

            var input_title_form =  $modal_edit_method.find('input[name="woocommerce_' + $yith_wpd_shipping_type + '_title"]');

            if( input_title_form.length > 0 ) {
                input_title_form.val(input_title.val());
            }

            var input_tax_status_form =  $modal_edit_method.find('select[name="woocommerce_' + $yith_wpd_shipping_type + '_tax_status"]');
            if( input_tax_status_form.length > 0 ) {
                input_tax_status_form.val(input_tax_status.val());
            }

            var input_method_cost_form =  $modal_edit_method.find('input[name="woocommerce_' + $yith_wpd_shipping_type + '_cost"]');
            if( input_method_cost_form.length > 0 ) {
                input_method_cost_form.val(input_method_cost.val());
            }

            var input_method_requires_form =  $modal_edit_method.find('select[name="woocommerce_free_shipping_requires"]');
            if( input_method_requires_form.length > 0 ) {
                input_method_requires_form.val(input_method_requires.val()).change();
            }

            var input_min_amount_form =  $modal_edit_method.find('input[name="woocommerce_free_shipping_min_amount"]');
            if( input_min_amount_form.length > 0 ) {
                input_min_amount_form.val(input_min_amount.val());
            }

            //Add Shipping Class Option
            $( '.yith-wpv-shipping-method-form-container_' + $yith_wpd_shipping_key + ' input.wc-shipping-zone-method-hidden-data').each( function( index ){
                var t = $(this),
                    key = t.attr( 'id' ),
                    field = key.replace( '_' + $yith_wpd_shipping_key, '' );

                if( ! field.match("^method_")
                    &&
                    ! field.match("^woocommerce_")
                    &&
                    ! field.match("^yith_wpd_")
                    &&
                    field !== 'min_amount'
                    &&
                    field !== 'type_id'
                    &&
                    field !== 'class_costs'
                    &&
                    field !== 'requires'
                ){
                    var extra_input_field   = $modal_edit_method.find( "#woocommerce_" + $yith_wpd_shipping_type + "_" + field  ),
                        hiddenInputFields   = $( "#" + key ).val();


                    if( extra_input_field.length > 0 ){
                        extra_input_field.val( hiddenInputFields );
                    }
                }

                if( field === 'class_costs' ){
                    $modal_edit_method.find( "#woocommerce_" + $yith_wpd_shipping_type + "_" + field  ).next( 'p' ).remove();
                }
            });

            //------------------------------------------------------------------------------------------------------------------------------

            yith_wpv_init_components();
        }
    } );

    $( document.body ).on( 'wc_backbone_modal_response', function(e,data,formData) {
        'use strict';

        // ADD NEW ZON
        if( typeof formData.yith_wpd_area_key != 'undefined' && formData.yith_wpd_area_key != null && formData.yith_wpd_area_key != '' ) {

            var $yith_wpdv_wc_shipping_zone_methods_list = $('.yith-wpdv-wc-shipping-zone-methods-list_' + formData.yith_wpd_area_key);
            var $yith_wpv_new_shipping_method_temp = $yith_wpdv_wc_shipping_zone_methods_list.find('li:not(.wc-shipping-zone-method-noshipping).wc-shipping-zone-method');
            var $wc_shipping_zone_method_hidden_data = $yith_wpdv_wc_shipping_zone_methods_list.find('.wc-shipping-zone-method-hidden-data');
            var $yith_wpv_shipping_methods_add_button = $yith_wpdv_wc_shipping_zone_methods_list.find('.wc-shipping-zone-methods-add-row');
            $yith_wpdv_wc_shipping_zone_methods_list.block(wc_cp_block_params);

            var data = {
                action: 'yith_wpv_shipping_add_new_shipping_method',
                data  : formData
            };

            $.post( yith_wpv_shipping_general.ajax_url, data, function ( response ) {

                 $yith_wpdv_wc_shipping_zone_methods_list.append( response );
                 $yith_wpdv_wc_shipping_zone_methods_list.append( $yith_wpv_new_shipping_method_temp );
                 $yith_wpdv_wc_shipping_zone_methods_list.append( $wc_shipping_zone_method_hidden_data );
                 $yith_wpdv_wc_shipping_zone_methods_list.find( '.wc-shipping-zone-method-noshipping' ).remove();
                 //$yith_wpdv_wc_shipping_zone_methods_list.prepend( $yith_wpv_shipping_methods_add_button );

                yith_wpv_init_components();

                $yith_wpdv_wc_shipping_zone_methods_list.unblock();

            } );

        }
        // Save Shipping Methods Data
        else if( typeof formData.yith_wpd_parent_key != '' && formData.yith_wpd_shipping_key != '' && formData.yith_wpd_shipping_type != '' ) {

            var $modal_edit_method = $('.wc-backbone-modal-edit-shipping-mode');

            if($modal_edit_method.length > 0) {

                var input_title = $('#method_title_' + formData.yith_wpd_shipping_key);
                var input_tax_status = $('#method_tax_status_' + formData.yith_wpd_shipping_key);
                var input_method_cost = $('#method_cost_' + formData.yith_wpd_shipping_key);

                var input_method_requires = $('#method_requires_' + formData.yith_wpd_shipping_key);
                var input_min_amount = $('#min_amount_' + formData.yith_wpd_shipping_key);

                switch (formData.yith_wpd_shipping_type) {

                    case 'flat_rate' :

                        if( input_title.length > 0 ) {
                            input_title.val(formData.woocommerce_flat_rate_title);
                        }

                        if( input_tax_status.length > 0 ) {
                            input_tax_status.val(formData.woocommerce_flat_rate_tax_status);
                        }

                        if( input_method_cost.length > 0 ) {
                            input_method_cost.val(formData.woocommerce_flat_rate_cost);
                        }

                        //Add Shipping Class Option
                        var frascaKeyStructure = "yith_vendor_data[zone_data][" + formData.yith_wpd_parent_key + "][zone_shipping_methods][" + formData.yith_wpd_shipping_key + "][";

                        $.each( formData, function( key, value ){
                            var field = key.replace( frascaKeyStructure, '' ).replace( ']', '' );

                            if( ! field.match("^method_")
                                &&
                                ! field.match("^woocommerce_")
                                &&
                                ! field.match("^yith_wpd_")
                                &&
                                field !== 'min_amount'
                                &&
                                field !== 'type_id'
                            ){

                                var extra_input_field = null;
                                extra_input_field = $( "#" + field + "_" + formData.yith_wpd_shipping_key );

                                if( extra_input_field ){
                                    var access_key = 'woocommerce_' + formData.yith_wpd_shipping_type + '_' + field;
                                    extra_input_field.val( formData[access_key] );
                                }
                            }
                        });

                        break;

                    case 'local_pickup' :

                        if( input_title.length > 0 ) {
                            input_title.val(formData.woocommerce_local_pickup_title);
                        }

                        if( input_tax_status.length > 0 ) {
                            input_tax_status.val(formData.woocommerce_local_pickup_tax_status);
                        }

                        if( input_method_cost.length > 0 ) {
                            input_method_cost.val(formData.woocommerce_local_pickup_cost);
                        }

                        break;

                    case 'free_shipping' :

                        if( input_title.length > 0 ) {
                            input_title.val(formData.woocommerce_free_shipping_title);
                        }

                        if( input_method_requires.length > 0 ) {
                            input_method_requires.val(formData.woocommerce_free_shipping_requires);
                        }

                        if( input_min_amount.length > 0 ) {
                            input_min_amount.val(formData.woocommerce_free_shipping_min_amount);
                        }

                        if( input_tax_status.length > 0 ) {
                            input_tax_status.val('');
                        }

                        if( input_method_cost.length > 0 ) {
                            input_method_cost.val('');
                        }

                        break;
                }
            }
        }
    } );

    $( document.body ).on('change' , '#yith-wpv-shipping-method-dropdown' , function() {
        var description = $( this ).find( 'option:selected' ).data( 'description' );
        $( this ).parent().find( '.wc-shipping-zone-method-description' ).remove();
        $( this ).after( '<p class="wc-shipping-zone-method-description">' + description + '</p>' );
        $( this ).closest( 'article' ).height( $( this ).parent().height() );
    });

    $( document.body ).on( 'click' , 'ul.yith-wpdv-wc-shipping-zone-methods-list li.wc-shipping-zone-method a.method_enabled' , function(e){
        'use strict';

        e.preventDefault();

        var $parent_key = $(this).data('parent-key');
        var $shipping_key = $(this).data('shipping-key');
        var $shipping_type = $(this).data('shipping-type');
        var $method_title = $(this).data('title');
        var $form_data_html = $('.yith-wpv-shipping-method-form-container_' + $shipping_key).html();

        $( this ).WCBackboneModal({
            template : 'wc-modal-edit-shipping-method',
            variable : {
                yith_wpd_parent_key : $parent_key,
                yith_wpd_shipping_key : $shipping_key,
                yith_wpd_shipping_type : $shipping_type,
                yith_wpd_shipping_title : $method_title,
                yith_wpd_form_data : $form_data_html,
            },
        });
    });

    $( document.body ).on( 'click' , 'ul.yith-wpdv-wc-shipping-zone-methods-list li .wc-shipping-zone-method-remove' , function(e){
        'use strict';

        e.preventDefault();

        var $parent = $(this).closest('.yith-wpv-new-shipping-method-temp') ;

        if( $parent.length > 0 ) {

            var $info_container = $( '.yith-wpv-shipping-method-form-container_' + $(this).data('index') );

            if( $info_container.length > 0 ) {
                $info_container.remove();
            }

            var $input_hidden = $parent.next('.wc-shipping-zone-method-hidden-data');

            if( $input_hidden.length > 0 ) {
                $input_hidden[0].remove();
            }

            $parent.remove();
        }
    });

    $( document ).on( 'click', '.yith-shipping-method-save-button', function(){
        $('#yith-save-shipping-settings-button').trigger( 'click' );

        var $table =  $(document).find('table.wc-shipping-zones');
        var $tbody = $table.find('tbody');

        $table.block( wc_cp_block_params );
    } );
    // End Shipping Methods

  //Select all countries
  $(document).on('click', '.yith-wpdv-wc-shipping-zone-trigger-all', function (e) {
    e.preventDefault();
    var t = $(this),
      index = t.data('index'),
      regions_list = '#yith-wcmv-zone-data-' + index,
      action = t.data('action');
    if (action == 'select-all') {
      $(regions_list + " > option").attr("selected", false);
      $("#yith-wcmv-shipping-select-all-regions").attr("selected", "selected");
    } else {
      $(regions_list + " > option").attr("selected", false);
    }

    $(regions_list).trigger('change');
  });

}(jQuery));
