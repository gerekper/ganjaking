jQuery(document).ready(function ($) {

    /** start of wccs zone pricing**/
    jQuery('#wccs_zp_countries').select2({        
        placeholder: "Choose Countries",        
        allowClear: true,
    });

    jQuery('#wccs_select_all_countries').on('click', function(e){
        e.preventDefault();
        jQuery("#wccs_zp_countries > option").prop("selected","selected");
        jQuery("#wccs_zp_countries").trigger("change");
    });

    jQuery( '#wccs_zp_toggle' ).on('click', function() {
        var $this = jQuery(this);        
        if ( jQuery($this).is(':checked') ) {
            var data = {
                action: 'wccs_toggle_zone_pricing',
                zp_toggle: 1,
                zp_nonce: variables.nonce
            };
        } else {
            var data = {
                action: 'wccs_toggle_zone_pricing',
                zp_toggle: 0,
                zp_nonce: variables.nonce
            };
        }

        jQuery.post( ajaxurl, data, function(response) {            
            if ( response == 'done' ) {
                if ( jQuery('#wccs_saved').length <= 0 ) {
                    jQuery($this).parent('label').append(`<span id="wccs_saved">(saved)</span>`);
                } else {
                    jQuery('#wccs_saved').html(`(saved)`);
                }

                setTimeout(function(){
                    jQuery('#wccs_saved').remove();
                }, 1500);
            }
        });
    });

    jQuery( document ).on( 'click', '.wccs_zp_delete', function(e) {
        e.preventDefault();        
        var parentel = jQuery(this).parents('tr'); 
        var data = {
            action: 'wccs_delete_zone_pricing',
            id: jQuery(this).data( 'id' ),                
            zp_nonce: variables.nonce
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
            dataType: 'html',
            beforeSend: function() {

            },
            success: function( response ) {
                if ( response != '' && response == 'done' ) {
                    jQuery( parentel ).remove();                        
                }
            }
        });

    });    

    jQuery( document ).on( 'click', '.wccs_zp_edit', function(e) {
        e.preventDefault();        
        var parentel = jQuery(this).parents('tr');
        var btn = jQuery('#wccs_add_zp');
        var id = jQuery(this).data( 'id' );
        var zp_name = jQuery(parentel).find( '.zp_name' ).text();
        var zp_countries = jQuery(parentel).find( '.zp_countries' ).text();
        zp_countries = zp_countries.split(', ');
        var zp_currency = jQuery(parentel).find( '.zp_currency' ).text();
        var zp_rate = jQuery(parentel).find( '.zp_rate' ).text();
        var zp_decimal = jQuery(parentel).find( '.zp_decimal' ).text();

        jQuery( '#wccs_zp_name' ).val( zp_name ).trigger( 'change' );
        jQuery( '#wccs_zp_countries' ).val( zp_countries ).trigger( 'change' );
        jQuery( '#wccs_zp_currency' ).val( zp_currency ).trigger( 'change' );
        jQuery( '#wccs_zp_rate' ).val( zp_rate ).trigger( 'change' );
        jQuery( '#wccs_zp_decimal' ).val( zp_decimal ).trigger( 'change' );
        if ( jQuery( '#zp_id' ).length > 0 ) {
            jQuery( '#zp_id' ).val( id ).trigger('change');
        } else {
            jQuery( btn ).after( `<input type="hidden" id="zp_id" name="zp_id" value="${id}" />` );
        }

    });

    jQuery( '#wccs_add_zp' ).on( 'click', function(e) {
        e.preventDefault();
        var zp_name = jQuery.trim( jQuery('#wccs_zp_name').val() );
        var zp_countries = jQuery('#wccs_zp_countries').val();
        var zp_currency = jQuery('#wccs_zp_currency').val();
        var zp_rate = jQuery.trim( jQuery('#wccs_zp_rate').val() );
        var zp_decimal = jQuery.trim( jQuery('#wccs_zp_decimal').val() );
        var zp_nonce = variables.nonce;
        var flag_1 = false;
        var flag_2 = false;
        var flag_3 = false;
        var flag_4 = false;
        var flag_5 = false;
        var error = `<p class="wccs-error">required field*</p>`;
        jQuery('.wccs_require_field').html('');

        if ( zp_name != '' ) {
            flag_1 = true;
        } else {
            jQuery('#wccs_zp_name').siblings('.wccs_require_field').html(error);
        }

        if ( zp_countries != '' ) {
            flag_2 = true;
        } else {
            jQuery('#wccs_zp_countries').siblings('.wccs_require_field').html(error);
        }

        if ( zp_currency != '' ) {
            flag_3 = true;
        } else {
            jQuery('#wccs_zp_currency').siblings('.wccs_require_field').html(error);
        }

        if ( zp_rate != '' ) {
            flag_4 = true;
        } else {
            jQuery('#wccs_zp_rate').parent('.wccs-flex-line').siblings('.wccs_require_field').html(error);
        }

        if ( zp_decimal != '' ) {
            flag_5 = true;
        } else {
            jQuery('#wccs_zp_decimal').parent('.wccs-flex-line').siblings('.wccs_require_field').html(error);
        }


        if ( flag_1 && flag_2 && flag_3 && flag_4 && flag_5 ) {

            if ( jQuery( '#zp_id' ).length > 0 ) {
                var data = {
                    action: 'wccs_add_zone_pricing',
                    zp_name: zp_name,
                    zp_countries: zp_countries,
                    zp_currency: zp_currency,
                    zp_rate: zp_rate,
                    zp_decimal: zp_decimal,
                    zp_nonce: zp_nonce,
                    id: jQuery( '#zp_id' ).val()
                };
            } else {
                var data = {
                    action: 'wccs_add_zone_pricing',
                    zp_name: zp_name,
                    zp_countries: zp_countries,
                    zp_currency: zp_currency,
                    zp_rate: zp_rate,
                    zp_decimal: zp_decimal,
                    zp_nonce: zp_nonce
                };
            }

            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
                dataType: 'html',
                beforeSend: function() {

                },
                success: function( response ) {                    
                    if ( response != '' ) {
                        window.location.reload();
                        // jQuery( '#wccs_zp_data' ).html( response );
                        // if ( jQuery( '#zp_id' ).length > 0 ) {
                        //     jQuery( '#zp_id' ).remove();
                        // }
                        // jQuery('#wccs_zp_name').val('').trigger('change');
                        // jQuery('#wccs_zp_countries').val('').trigger('change');
                        // jQuery('#wccs_zp_currency').val('').trigger('change');
                        // jQuery('#wccs_zp_rate').val('').trigger('change');
                        // jQuery('#wccs_zp_decimal').val('').trigger('change');
                    } else {
                        alert('Zone pricing didn\'t update.')
                    }
                }
            });
        }
    });
    /** end of wccs zone pricing**/

    jQuery( '.wccs_add_single_currency' ).on( 'click', function(e) {
        e.preventDefault();
        //debugger;
        var val = $(this).siblings('.wccs_get_defined_currency').val();

        if ( $(this).data('type') == 'single' ) {
            if ( val != '' && $('input[name="wccs_cfa_code[]"][value="'+ val +'"]').length <= 0 ) {
                $( '#wccs_coupon_amount_for_currencies_wrapped' ).append( 
                    `<p class=" form-field discount_type_field">
                        <label for="wccs_cfa_value">
                            <strong>Coupon amount (${val}): </strong>                    
                        </label>
                        <input type="hidden" name="wccs_cfa_code[]" value="${val}" />
                        <input type="text" id="wccs_cfa_value" name="wccs_cfa_value[]" Placeholder="auto" value="" />
                        <a href="#" class="ml-10 button button-secondary wccs_cfa_remove">remove</a>
                    </p>`
                );
            }
        }

        if ( $(this).data('type') == 'multiple' ) {
            if ( val != '' && $('input[name="wccs_cfa_minmax_code[]"][value="'+ val +'"]').length <= 0 ) {
                $( '#wccs_coupon_minmax_amount_for_currencies_wrapped' ).append( 
                    `<p class=" form-field discount_type_field">
                        <input type="hidden" name="wccs_cfa_minmax_code[]" value="${val}" />
                        
                        <span class="wccs_form_control">
                            <label for="wccs_cfa_min_value">
                                <strong>Minimum spend (${val}): </strong>                    
                            </label>								
                            <input type="text" id="wccs_cfa_min_value" name="wccs_cfa_min_value[]" Placeholder="auto" value="" />
                            <a href="#" class="ml-10 button button-secondary wccs_cfa_remove">remove</a>
                        </span>

                        <span class="wccs_form_control">
                            <label for="wccs_cfa_min_value">
                                <strong>Maximum spend (${val}): </strong>                    
                            </label>
                            <input type="text" id="wccs_cfa_max_value" name="wccs_cfa_max_value[]" Placeholder="auto" value="" />
                        </span>                        
                    </p>`
                );
            }
        }

    } );

    jQuery( '.wccs_add_all_currencies' ).on( 'click', function(e) {
        e.preventDefault();

        var options = $('.wccs_get_defined_currency option');
        var values = $.map(options ,function(option) {
            if ( option.value != '' )
                return option.value;
        });

        //debugger;

        if ( $(this).data('type') == 'single' ) {

            $.each( values, function( i, val ) {
                if ( val != '' && $('input[name="wccs_cfa_code[]"][value="'+ val +'"]').length <= 0 ) {
                    $( '#wccs_coupon_amount_for_currencies_wrapped' ).append( 
                        `<p class=" form-field discount_type_field">
                            <label for="wccs_cfa_value">
                                <strong>Coupon amount (${val}): </strong>                    
                            </label>
                            <input type="hidden" name="wccs_cfa_code[]" value="${val}" />
                            <input type="text" id="wccs_cfa_value" name="wccs_cfa_value[]" Placeholder="auto" value="" />
                            <a href="#" class="ml-10 button button-secondary wccs_cfa_remove">remove</a>
                        </p>`
                    );
                }
            } );
        }

        if ( $(this).data('type') == 'multiple' ) {            
            $.each( values, function( i, val ) {
                if ( val != '' && $('input[name="wccs_cfa_minmax_code[]"][value="'+ val +'"]').length <= 0 ) {
                    $( '#wccs_coupon_minmax_amount_for_currencies_wrapped' ).append( 
                        `<p class=" form-field discount_type_field">
                            <input type="hidden" name="wccs_cfa_minmax_code[]" value="${val}" />
                            
                            <span class="wccs_form_control">
                                <label for="wccs_cfa_min_value">
                                    <strong>Minimum spend (${val}): </strong>                    
                                </label>								
                                <input type="text" id="wccs_cfa_min_value" name="wccs_cfa_min_value[]" Placeholder="auto" value="" />
                                <a href="#" class="ml-10 button button-secondary wccs_cfa_remove">remove</a>
                            </span>

                            <span class="wccs_form_control">
                                <label for="wccs_cfa_min_value">
                                    <strong>Maximum spend (${val}): </strong>                    
                                </label>
                                <input type="text" id="wccs_cfa_max_value" name="wccs_cfa_max_value[]" Placeholder="auto" value="" />
                            </span>                            
                        </p>`
                    );
                }
            } );
        }

    } );

    $(document).on( 'click', '.wccs_cfa_remove', function(e) {
        e.preventDefault();
        $(this).parents('.discount_type_field').remove();
    } );

    //payment gateway
    $(document).on('click', '.wccs-payment-gateway-td button', function (e) {
        e.preventDefault();
        var code = $(this).data('code');
        // alert(code);
        $(this).toggleClass('wccs-close wccs-open');
        $(this).siblings('div.wccs_payment_gateways_container').slideToggle(100);
    });

    // add currency
    $(document).on("change", "#wccs_add_currency", function(){
        //debugger;
        $(this).attr('disabled', 'disabled');
        var value = $(this).val();
        var label = $(this).find("option:selected").text();
        var nonce = variables.nonce;
        
        //prepare ajax
        var data = {
            'action': 'wccs_add_currency',
            'code': value,
            'label': label,
            'nonce': nonce
        };

        $.post(variables.ajaxurl, data, function (response) {
            var obj = JSON.parse(response);
            if (obj.status) {
                $('#wccs_currencies_list').append(obj.html);
                
                $('.flags').prettyDropdown({
                    classic: true,
                    width: 110,
                    height: 30,
                    customClass: 'wccs_arrow'

                });
                
                $('#wccs_currencies_table').show();
            }else{
                console.log('wccs error: noting to add');
            }
        });
        
        // remove option
        $(this).find('option[value='+value+']').remove();
        
        // clear select or return to default
        if($(this).find('option').length > 1){
            $(this).val('');
            $(this).attr('disabled', false);
        }else{
            $(this).hide();
        }
    });
    
    // remove currency
    $(document).on("click", ".wccs_remove_currency", function(){
        $(this).attr('disabled', 'disabled');
        var value = $(this).data('value');
        var label = $(this).data('label');
        
        // add to select
        $('#wccs_add_currency').append(new Option(label, value));
        
        // sort select
        var opts_list = $('#wccs_add_currency').find('option');
        opts_list.sort(function(a, b) { return $(a).val() > $(b).val() ? 1 : -1; });
        $('#wccs_add_currency').html('').append(opts_list);
        $('#wccs_add_currency').val('');
        
        // remove currency
        $(this).closest('tr').remove();
        if($('#wccs_currencies_table tbody tr').length == 0){
            $('#wccs_currencies_table').hide();
        }
    });
    
    // update all currencies rates ajax
    $(document).on("click", "#wccs_update_all", function(){
        var button = $(this);
        button.attr('disabled', 'disabled');
        
        //prepare ajax
        var data = {
            'action': 'wccs_update_all'
        };

        $.post(variables.ajaxurl, data, function (response) {
            var obj = JSON.parse(response);
            if (obj.status) {
                var data = obj.rates;
                for(var k in data) {
                    if(data.hasOwnProperty(k)) {
                        $('input[name="wccs_currencies['+k+'][rate]"]').val(data[k]);
                    }
                }
                button.attr('disabled', false);
            }else{
                console.log('wccs error: problem on updating');
                button.attr('disabled', false);
            }
        });
    });
    
    // update single currency rate ajax
    $(document).on("click", ".wccs_update_rate", function(){
        var button = $(this);
        button.attr('disabled', 'disabled');
        var code = $(this).data('code');
        var nonce = variables.nonce;
        
        //prepare ajax
        var data = {
            'action': 'wccs_update_single_rate',
            'code': code,
            'nonce': nonce
        };

        $.post(variables.ajaxurl, data, function (response) {
            var obj = JSON.parse(response);
            if (obj.status) {
                $('input[name="wccs_currencies['+code+'][rate]"]').val(obj.rate);
                button.attr('disabled', false);
            }else{
                console.log('wccs error: problem on updating');
                button.attr('disabled', false);
            }
        });
    });
    
    $(document).on("change", "input[name='wccs_update_type']", function(){
        ShowAPISection($(this).val());
    });
    
    ShowAPISection($("input[name='wccs_update_type']:checked").val());
    
    $(document).on("change", "input[name='wccs_show_in_menu']", function(){
        ShowMenuSection();
    });
    
    ShowMenuSection();
    
    $(document).on("change", "input[name='wccs_admin_email']", function(){
        ShowEmailSection();
    });
    
    ShowEmailSection();
    
    $(document).on("change", "input[name='wccs_sticky_switcher']", function(){
        ShowStickySection();
    });
    
    ShowStickySection();

    $(document).on("change", "input[name='wccs_pay_by_user_currency']", function(){
        ShowFixedCouponSection();
    });
    
    ShowFixedCouponSection();
    
    $(document).on("change", "input[name='wccs_currency_by_lang']", function(){
        ShowWPMLSection();
    });
    
    ShowWPMLSection();

    $(document).on("change", "input[name='wccs_currency_by_location']", function(){
        ShowLocationAPI();
    });
    
    ShowLocationAPI();
    
    $("#wccs_currencies_list").sortable({
        cursor: "grabbing"
    });
    
    var dd = $('.flags').prettyDropdown({
	classic: true,
        width: 110,
        height: 30,
        customClass: 'wccs_arrow'
        
    });
});


function ShowAPISection(radio){
    if(radio == 'fixed'){
        jQuery('.wccs_api_section').hide();
    }else{
        jQuery('.wccs_api_section').show();
    }
}

function ShowMenuSection(){
    if(jQuery("input[name='wccs_show_in_menu']").is(':checked')){
        jQuery('.wccs_menu_section').show()
    } else {
        jQuery('.wccs_menu_section').hide();
    }
}

function ShowEmailSection(){
    if(jQuery("input[name='wccs_admin_email']").is(':checked')){
        jQuery('.wccs_email_section').show()
    } else {
        jQuery('.wccs_email_section').hide();
    }
}

function ShowStickySection(){
    if(jQuery("input[name='wccs_sticky_switcher']").is(':checked')){
        jQuery('.wccs_sticky_section').show()
    } else {
        jQuery('.wccs_sticky_section').hide();
    }
}

function ShowFixedCouponSection(){
    if(jQuery("input[name='wccs_pay_by_user_currency']").is(':checked')){
        jQuery('.wccs_fixed_coupon_amount_wrapper').show()
    } else {
        jQuery('.wccs_fixed_coupon_amount_wrapper').hide();
    }
}

function ShowWPMLSection(){
    if(jQuery("input[name='wccs_currency_by_lang']").is(':checked')){
        jQuery('.wccs_wpml_lang_wrapper').show()
    } else {
        jQuery('.wccs_wpml_lang_wrapper').hide();
    }
}

function ShowLocationAPI(){
    if(jQuery("input[name='wccs_currency_by_location']").is(':checked')){
        jQuery('.wccs_ipapi_key').show()
    } else {
        jQuery('.wccs_ipapi_key').hide();
    }
}