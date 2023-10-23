jQuery(document).ready(function($) {

    $( ".attributes .fields" ).sortable({
        cursor: "move",
        scrollSensitivity: 10,
        tolerance: "pointer",
        axis: "y",
        stop: function(event, ui) {
            var list = ui.item.parents('.fields'),
                fields = new Array();
            $('input[type="checkbox"]', list).each(function(i){
                fields[i] = $(this).val();
            });

            list.next().val( fields.join(',') );
        }
    });

    // ############### PANEL OPTIONS ###################

    $( 'input[type="checkbox"]').on( 'woocompare_input_init change', function(){

        if( ! $(this).is(':checked') ) {
            $( '[data-deps="' + this.id + '"]' ).parents('tr').fadeOut();
        }
        else {
            $( '[data-deps="' + this.id + '"]' ).parents('tr').fadeIn();
        }
    }).trigger('woocompare_input_init');

    // ################ SHARE PANEL ####################

    // select2 to select socials
    $(".yith-woocompare-chosen").select2({
        placeholder: "Select social..."
    });


    // ##################### SHORTCODE PANEL ####################

    var sc_preview = $( '.shortcode-preview' ),
        blank_shortcode = sc_preview.html();

    $('.yith_woocompare_tab_shortcode_products').each( function(){

        $(this).on( 'change', function(){

            var value = ( this.type == 'checkbox' && ! $(this).is( ':checked' ) ) ? $(this).data('novalue') : $(this).val(),
                name  = this.name.replace( 'yith_', '').replace('[]', ''),
                shortcode,
                attr;

            if( ! value  ) {
                sc_preview.html( blank_shortcode );
                return;
            }

            // else add attr
            shortcode = blank_shortcode.replace(']', '');

            attr = name + '="' + value + '"';
            sc_preview.html( shortcode + ' ' + attr + ']' )

        });
    });

});