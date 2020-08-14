(function ($) {
    if( yith_wcfm.tab == 'settings' ){
        $('#yith_wcfm_flush_rewrite_rules').on( 'click', function(){
            var t = $( this ),
                message_wrapper = $('#yith-wcfm-flushed-message');

            if( typeof message_wrapper != 'undefined' ){
                message_wrapper.remove();
            }

            if( confirm( yith_wcfm.flush_confirm_message ) ){
                t.prop( 'disabled', true );
                $.ajax({
                        url : ajaxurl,
                        data: {
                            action: 'yith_wcfm_flush_rewrite_rules'
                        },
                        success: function (data) {
                            t.prop( 'disabled', false );
                            t.parents('tr').find('th').append( '<span id="yith-wcfm-flushed-message">' +  yith_wcfm.flushed_message +'</span>' );
                        }
                    }
                );
            }
        } );
    }

})(jQuery);
