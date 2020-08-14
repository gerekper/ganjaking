/* global ajaxurl */
( function ( $ ) {

    var yith_pos_validator = {
        after_validation: function ( action, $field ) {
            var message        = $field.data( 'message' ),
                $select2       = $field.is( 'select.enhanced' ) ? $field.next( '.select2' ) : false,
                $beforeMessage = $select2 ? $select2 : $field;

            $beforeMessage.next( '.validate-error' ).remove();

            if ( action !== 'valid' ) {
                $field.addClass( 'invalid' );
                $select2 && $select2.addClass( 'invalid' );

                if ( typeof message !== 'undefined' ) {
                    $beforeMessage.after( '<span class="validate-error">' + message + '</span>' );
                }
            } else {
                $field.removeClass( 'invalid' );
                $select2 && $select2.removeClass( 'invalid' );
            }
        },
        check_email     : function ( email ) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test( email );
        },
        check_website   : function ( website ) {
            var regex = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
            return regex.test( website );
        },
        validateRequiredField        : function () {
            var isEmpty = !$( this ).val() || $( this ).val() == null;
            yith_pos_validator.after_validation( isEmpty ? '' : 'valid', $( this ) );
        }
    };

    /* Validate for YITH required fields */
    $( document ).on( 'focusout validate_field', '.yith-plugin-ui input[required]', yith_pos_validator.validateRequiredField );
    $( document ).on( 'focusout validate_field', '.yith-pos-required-field', yith_pos_validator.validateRequiredField );

    /* Validate for YITH required select2 fields */
    $( document ).on( 'select2:close validate_field', '.yith-plugin-fw--required select.enhanced', yith_pos_validator.validateRequiredField );

    /* Validate username and email fields */
    $( document ).on( 'focusout validate_field', '.validate-user', function () {
        var $this = $( this );
        if ( $this.val() !== '' && $this.val() != null ) {

            if ( $this.hasClass( 'validate-email' ) && !yith_pos_validator.check_email( $this.val() ) ) {
                yith_pos_validator.after_validation( '', $this );
                return;
            }

            var data = {
                'action': 'yith_pos_check_user_login',
                'field' : $this.hasClass( 'validate-email' ) ? 'email' : 'login',
                'value' : $this.val().trim()
            };

            $.ajax( {
                        type   : 'POST',
                        data   : data,
                        url    : ajaxurl,
                        success: function ( response ) {
                            yith_pos_validator.after_validation( response.is_valid === 1 ? 'valid' : '', $this );
                        }
                    } );
        }
    } );

} )( jQuery );