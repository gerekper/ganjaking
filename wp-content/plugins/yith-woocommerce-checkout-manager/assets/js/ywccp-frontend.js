jQuery(document).ready(function ($) {
    "use strict";

    var input_elem = $( 'form[name="checkout"]').find( 'p.form-row input, p.form-row textarea' ),
        abbr        = ' <abbr class="required" title="required">*</abbr>',
        error       = '<span class="ywccp_error"></span>', // init error

        ywccp_ismail = function( val ){
            var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

            return re.test( val );
        },
        ywccp_validatevat = function( vat ) {

            var country = $('#billing_country');

            if( typeof checkVATNumber == 'undefined' || ! country.length || ! ywccp_front.vat_validation_enabled ){
                return true;
            }

            // check if vat number has country code
            var prefix       = vat.substr( 0, 2 ).toUpperCase(),
                country_val  = country.val();

            if( prefix !== country_val ) {
                //prepend country to vat
                vat = country_val + vat;
            }

            return checkVATNumber ( country_val, vat );
        },

        ywccp_error = function( elem, msg ){

            if( ! elem.next( '.ywccp_error' ).length ) {
                elem.after( error );
            }
            // add error
            elem.next( '.ywccp_error' ).html( msg );
        };

    if( input_elem.length ) {
        $.each( input_elem, function(){

            var elem    = $(this),
                tooltip = elem.data('tooltip'),
                parent  = elem.closest( 'p.form-row' );

            elem.on( 'blur', function(){

                var t     = $(this),
                    value = t.val(),
                    msg   = '';

                if( ! ywccp_front.validation_enabled ) {
                    return;
                }

                if( ! value && parent.hasClass( 'validate-required' ) ) {
                    msg = ywccp_front.err_msg;
                    ywccp_error( t, msg );
                }
                else if ( value && parent.hasClass( 'validate-vat' ) && ! ywccp_validatevat( value ) ) {
                    ywccp_error( t, ywccp_front.err_msg_vat );
                }
                else if( value && parent.hasClass( 'validate-email' ) && ! ywccp_ismail( value ) ){
                    ywccp_error( t, ywccp_front.err_msg_mail );
                }
                else {
                    elem.next( '.ywccp_error' ).remove();
                }
            });

            if( typeof tooltip != 'undefined' && tooltip != '' && typeof $.fn.qtip != 'undefined'  ) {
                elem.qtip({
                    content: { text: tooltip },
                    show: { event: 'focus' },
                    style: { classes: 'ywccp_tooltip' },
                    position: {
                        my: 'bottom center',
                        at: 'top center',
                        viewport: $(window)
                    }
                });
            }
        });
    }

    var select = $('.ywccp-multiselect-type, select.select'),
        datepicker = $('.ywccp-datepicker-type'),
        timepicker = $('.ywccp-timepicker-type');

    if ( select && typeof $.fn.select2 != 'undefined' ) {
        $.each( select, function () {
            var s = $(this),
                sid = s.attr('id');

            if( $('#s2id_' + sid ).length ) {
                return;
            }

            s.select2({
                placeholder: s.data('placeholder')
            });
        });
    }

    if ( typeof $.fn.datepicker != 'undefined' && datepicker ) {
        $.each( datepicker, function () {
            $(this).datepicker({
                dateFormat: $(this).data('format') || "dd-mm-yy",
                changeYear: ywccp_front.datepicker_change_year,
                changeMonth: ywccp_front.datepicker_change_month,
                yearRange: ywccp_front.datepicker_year_range,
                minDate: ywccp_front.datepicker_min_date,
                maxDate: ywccp_front.datepicker_max_date,
                beforeShow: function(){
                    setTimeout(function( date ){
                        $('#ui-datepicker-div').wrap('<div class="yith_datepicker"></div>').css('z-index', 99999999999999);
                        $('#ui-datepicker-div').show();
                    }, 0);
                },
                beforeShowDay: function(date){                    
                    if( ywccp_front.datepicker_allowed_days.includes( date.getDay() ) ){          
                      return [true];
                    }else{              
                      return [false];
                    }
                },
                onClose:function(){
                    $('#ui-datepicker-div').hide();
                    $('#ui-datepicker-div').unwrap();
                }
            });
        });
    }

    if ( typeof $.fn.timepicki != 'undefined' && timepicker ) {
        $.each( timepicker, function () {
            $(this).timepicki({
                reset: true,
                disable_keyboard_mobile: true,
                show_meridian: ywccp_front.time_format,
                max_hour_value: ywccp_front.time_format ? '12' : '23',
                min_hour_value: ywccp_front.time_format ? '1' : '0',
                overflow_minutes:true,
                increase_direction:'up'
            });
        });

        $(document).on('click', '.reset_time', function (ev) {
            ev.preventDefault();
        });
    }


    /****  Checkout conditions  **************/
    var get_all_conditions = function(){
        return jQuery.parseJSON(ywccp_front.conditions);
    };

    var get_conditions_by_field = function( field ){
        return get_all_conditions()[field];
    };


    var get_input_value = function( input_name ){
        var value = null,
            input = $('*[name='+input_name + ']');
        if( input.length > 1 ){
            value = get_value_for_multiple( input );
        }else{
            if( input.attr('type') == 'checkbox' ){
                value = input.is(':checked');

            }else if( input.attr('type') == 'radio' ){
                value = $('#'+input_name + ':checked').val();
            }else{
                value = input.val();
            }
        }
        return value;
    };


    var get_value_for_multiple = function( input ){
        var value = null;
        input.each( function(i){
            if( input[i].type == 'radio' ){
                if( input[i].checked ){
                    value = input[i].value;
                    return false;
                }
            }
        } );
        return value;
    }

    var validate_condition = function( condition ){

        var input_name  =   condition.input_name,
            type        =   condition.type,
            action      =   condition.action,
            required    =   condition.required,
            value       =   condition.value,
            input_value =   get_input_value(input_name),
            is_valid    =   false;

        switch( type ){

            case 'is-set':
                if( input_value ){
                    is_valid = true;
                }
                break;

            case 'is-empty':
                if( !input_value ){
                    is_valid = true;
                }
                break;

            case 'has-value':
                var values = value.split(',');
                jQuery.each( values, function( index, single_item ){
                    if( String(input_value) == String(single_item) ){
                        is_valid = true;
                    }
                });

                break;

            case 'has-not-value':
                if( input_value != value ){
                    is_valid = true;
                }
                break;

            default:
                break;

        }
        return is_valid;

    };


    // Validate and check single field
    var validate_field = function( field ){

        var conditions = get_conditions_by_field( field );
        if( typeof conditions === "undefined" || conditions.length === 0 ){
            return null;
        }

        var n_conditions    = conditions.length,
            status = { show: null, hide: null, set_required: null };


        for( var i=0; i<n_conditions; i++ ){

            var condition   =   conditions[i],
            	condition_is_valid = validate_condition( condition ),
            	condition_action = condition.action,
            	condition_required = condition.required,
				wc_condition_required = condition.wc_required,
            	input_name = condition.input_name;

            if( input_name != 'products' ){
                if( condition_action === 'show' && status.show != 'no' ){
                    if( !condition_is_valid  ){
                        status.show = 'no';
                        status.set_required = 'no';
                        status.hide = 'yes';
                    }else{
                        if( condition_required == 1 ){
                            status.set_required = 'yes';
                        }
                        status.show = 'yes';
                        status.hide = 'no';
                    }
                }else if( condition_action === 'hide' && status.hide != 'no' ){
                    if( !condition_is_valid ){
                        status.hide = 'no';
                        status.show = 'yes';

                    }else{
                        status.hide = 'yes';
                        status.show = 'no';
                    }
                }
            }else if ( condition_required == 1 ){
                status.set_required = 'yes';
                status.show = 'yes';
            }else if ( wc_condition_required ){
                status.set_required = 'yes';
                status.show = 'yes';
            }


        };

        return status;

    };


    $('form.checkout').on( 'change','input, select',function(event) {
        check_all_checkout_field(event);
    });

    $( 'form.checkout' ).on( 'submit', function(event){
        check_all_checkout_field(event);
    } );

    $( document.body ).on( 'yith_wccp_i18n_locale_done', function(event){
        check_all_checkout_field(event);
    } );

    // Check on all checkout fields: show, hide or required?
    var check_all_checkout_field = function(event){
        $("form.checkout").find('input, textarea, select, h3[data-name]').each(function(){
            var name        = $(this).attr('name') || $(this).attr('data-name'),
                form_row    = $(this).closest('.form-row').length ? $(this).closest('.form-row') : $(this),
                status      = validate_field(name),
                required    = false;

            if( status != null ){
                if( status.show == 'yes' ){
                    form_row.show();
                    if( status.set_required == 'yes' ){
                        required = true;
                    }
                }else if( status.hide == 'yes' ){
                    form_row.hide();
                }

                set_required_field( event.type, form_row, required );
            }
        });

    };


    // Set as required or not each field in accordion to conditions
    var set_required_field = function(event,form_row,required){

        var required_html = '<abbr class="required" title="required">*</abbr>',
            optional = '<span class="optional">(optional)</span>';
        if( required == true ){
            form_row.addClass( 'validate-required' );
            if( form_row.find('label').find('abbr.required').length == 0 ){
                form_row.find('label').find('.optional').remove();
                form_row.find('label').append(required_html);
            }
        }else{
            form_row.removeClass( 'validate-required woocommerce-validated woocommerce-invalid woocommerce-invalid-required-field' );
            if( form_row.find('label').find('.optional').length == 0 ) {
                form_row.find('label').append(optional);
                form_row.find('abbr.required').remove();
            }
        }

    };
    
    $('form.checkout').find('input, select').each( function(){
        const fields_to_exclude = ywccp_front.field_ids_to_exclude;
        let id = $( this ).attr( 'id' );
        if( $.isArray( fields_to_exclude ) && $.inArray( id, fields_to_exclude, 0 ) < 0 ){
            $( this ).trigger( 'change' );
        }
    } );

    // restore ship to different address to old behaviour
    if( ywccp_front.ship_different_address_old_behaviour ){
        var shipToDifferentChecked = $( '#ship-to-different-address-checkbox' ).is(':checked');
        $( '.woocommerce-shipping-fields__field-wrapper' ).hide();
        $( document.body ).on( 'init_checkout', function(){
            $( '#ship-to-different-address-checkbox' ).prop( 'checked', shipToDifferentChecked ).change();
            $( '.woocommerce-shipping-fields__field-wrapper' ).toggle( true );
        });
    }
});
