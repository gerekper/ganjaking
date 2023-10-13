
var WCML_Setup = WCML_Setup || {};

jQuery( function($){

    WCML_Setup.init = function(){

        $(function() {

            $('.wcml-setup-form').on( 'click', 'a.submit', function(){

                var form = $(this).closest('form');
                form.attr('action', $(this).attr('href') );
                if (form.get(0).checkValidity()) {
                    form.submit();
                } else {
                    form.get(0).reportValidity();
                }
                return false;

            });

        });

    }

    WCML_Setup.init();

});
