jQuery( function($){

    var sshipLicense = {

        request : function( action, args, callback ) {

            args = args !== '' && args instanceof jQuery ? window.ADS.serialize(args) : args;

            $.ajaxQueue( {
                url     : ajaxurl,
                data: { action: 'sship_license', sship_action: action, args: args },
                type    : 'POST',
                dataType: 'json',
                success : callback
            });
        },

        formRender : function ( response ) {

            var tmpl = $('#tmpl-license-form').html(),
                target = $('#license-form');
            if( response ) {

                if( response.hasOwnProperty( 'error' ) ) {
                    window.ADS.notify( response.error, 'danger' );
                    window.ADS.btnUnLock( $('.js-activate') );
                } else {

                    if( response.hasOwnProperty( 'message' ) ) {
						setTimeout( location.replace('/wp-admin/admin.php?page=sshiplist'), 3000 );
                        window.ADS.notify( response.message, 'success' );
                    }

                    target.html( window.ADS.objTotmpl( tmpl, response ) );
                    setTimeout( window.ADS.switchery( target ), 300 );
                }
            }
        },

        form : function () {

            this.request( 'get_license', '', this.formRender );
        },

        handler : function() {

            var $this = this;

            $(document).on( 'click', '.js-activate', function () {

                window.ADS.btnLock( $(this) );

                $this.request( 'save_license', $('#license-form'), $this.formRender );
            } );

        },

        init: function () {

            this.handler();
            this.form();
        }
    };

    sshipLicense.init();
} );