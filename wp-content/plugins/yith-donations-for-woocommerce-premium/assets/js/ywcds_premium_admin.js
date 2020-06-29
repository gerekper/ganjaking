jQuery(document).ready(function($){
    var  admin_field_init   =   function(){

        var button_select   =   $('#ywcds_button_style_select'),
            button_typ      =   $('.donation-button-typography'),
            button_col      =   $('#ywcds_text_color'),
            button_bg       =   $('#ywcds_bg_color'),
            button_col_hov  =   $('#ywcds_text_hov_color'),
            button_bg_hov   =   $('#ywcds_bg_hov_color');


        if( button_select.val()=="wc" ){
            button_typ.hide();
            button_col.parents( 'tr').hide();
            button_bg.parents( 'tr').hide();
            button_col_hov.parents( 'tr').hide();
            button_bg_hov.parents( 'tr').hide();
        }
        else{

            button_typ.show();
            button_col.parents( 'tr').show();
            button_bg.parents( 'tr').show();
            button_col_hov.parents( 'tr').show();
            button_bg_hov.parents( 'tr').show();
        }

        button_select.on('change', function(){

            var t   =   $(this);

            if( t.val()=="wc" ){
                button_typ.hide();
                button_col.parents( 'tr' ).hide();
                button_bg.parents( 'tr' ).hide();
                button_col_hov.parents( 'tr' ).hide();
                button_bg_hov.parents( 'tr' ).hide();
            }
            else{

                button_typ.show();
                button_col.parents( 'tr' ).show();
                button_bg.parents( 'tr' ).show();
                button_col_hov.parents( 'tr' ).show();
                button_bg_hov.parents( 'tr' ).show();
            }

        });
    }





    $('body').on('ywcds-admin-field-init', function () {
        admin_field_init();

    }).trigger( 'ywcds-admin-field-init' );


});
