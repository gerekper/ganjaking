/**
 * global yith_wccos_params
 */
jQuery( function ( $ ) {
    $( '.yith-wccos-color-picker' ).wpColorPicker();

    // hide preview button and View order status button
    $( '#edit-slug-box' ).hide();
    $( '#preview-action' ).hide();

    var slug                               = function ( str ) {
            str = str.replace( /^\s+|\s+$/g, '' ); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = yith_wccos_params.slug_from;
            var to   = yith_wccos_params.slug_to;
            for ( var i = 0, l = from.length; i < l; i++ ) {
                str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
            }


            str = str.replace( new RegExp( yith_wccos_params.slug_allowed, 'g' ), '' ) // remove invalid chars
                .replace( /\s+/g, '-' ) // collapse whitespace and replace by -
                .replace( /-+/g, '-' ); // collapse dashes

            if ( str.length >= 1 ) {
                return str.substr( 0, 17 );
            }
            return str;
        },
        slug_field                         = $( '#slug' ),
        title                              = $( '#title' ),
        status_type                        = $( '#status_type' ),
        create_slug                        = true,
        recipients                         = $( '#recipients' ),
        mail_settings_info_total_container = $( '#mail-settings-info' ).parent(),
        tab_mail_settings                  = $( 'li.tabs' ).next(),
        custom_recipient_container         = $( '#custom_recipient-container' ).parent(),
        field_can_pay                      = $( '#can-pay' ),
        field_can_cancel                   = $( '#can-cancel' ),
        field_download_permitted           = $( '#downloads-permitted' ),
        field_display_in_reports           = $( '#display-in-reports' ),
        field_next_actions                 = $( '#nextactions' ),
        check_mail_settings_visibility     = function () {
            if ( status_type.val() !== 'custom' ) {
                tab_mail_settings.hide();
                mail_settings_info_total_container.show();
                recipients.val( '' );
            } else {
                tab_mail_settings.show();
                mail_settings_info_total_container.hide();
            }

            if ( recipients.val() && recipients.val().indexOf( 'custom-email' ) !== -1 ) {
                custom_recipient_container.show();
            } else {
                custom_recipient_container.hide();
            }
        },
        reset_fields                       = function () {
            field_can_pay.attr( 'checked', false );
            field_can_cancel.attr( 'checked', false );
            field_download_permitted.attr( 'checked', false );
            field_display_in_reports.attr( 'checked', false );
        },
        check_wc_status_selected           = function () {
            reset_fields();
            switch ( slug_field.val() ) {
                case 'pending':
                case 'failed':
                    field_can_pay.attr( 'checked', true );
                    field_can_cancel.attr( 'checked', true );
                    break;

                case 'completed':
                case 'processing':
                    field_download_permitted.attr( 'checked', true );
                    field_display_in_reports.attr( 'checked', true );
                    break;

                case 'on-hold':
                    field_display_in_reports.attr( 'checked', true );
                    break;

                default:

            }
        };

    $( '.yith-wccos-select2' ).select2( { width: '100%' } );

    if ( slug_field.val().length < 1 ) {
        if ( title.val().length > 0 ) {
            // Fix for drafted statuses
            slug_field.val( slug( title.val() ) );
            slug_field.trigger( 'change' );
        }
        title.on( 'keyup', function () {
            if ( create_slug ) {
                slug_field.val( slug( title.val() ) );
                slug_field.trigger( 'change' );
            }
        } );
    } else {
        create_slug = false;
        status_type.parent().css( { position: 'relative' } ).append( '<div style="position:absolute; width:100%; height:100%; z-index: 10;background: rgba(255,255,255,0.6);top:0;left:0;"></div>' );
        slug_field.prop( 'readonly', true );
    }

    slug_field.on( 'keyup', function () {
        var creted_slug = slug( slug_field.val() );
        if ( creted_slug !== slug_field.val() ) {
            slug_field.val( creted_slug );
        }
    } );

    slug_field.on( 'change keyup', function () {
        if ( $( this ).val().length < 1 ) {
            $( this ).addClass( 'yith-wccos-required-field' );
        } else {
            $( this ).removeClass( 'yith-wccos-required-field' );
        }
    } ).trigger( 'change' );

    status_type.on( 'change', function () {
        if ( $( this ).val() === 'custom' ) {
            create_slug = true;
            slug_field.val( slug( title.val() ) );
            slug_field.prop( 'readonly', false );

        } else {
            create_slug = false;
            slug_field.val( $( this ).val() );
            slug_field.prop( 'readonly', true );
        }
        slug_field.trigger( 'change' );
        check_mail_settings_visibility();
        check_wc_status_selected();
    } );

    if ( status_type.val() !== 'custom' ) {
        recipients.parent().hide();
    } else {
        mail_settings_info_total_container.hide();
    }

    // MAIL Fields control
    check_mail_settings_visibility();

    recipients.on( 'change', function () {
        //alert($(this).val());
        check_mail_settings_visibility();
    } );
} );