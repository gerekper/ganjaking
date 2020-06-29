(function($){
    var license_notice  = $( '#yith-license-notice' );

        $('body').on( 'click', '#yith-license-notice button.notice-dismiss', function(){
            $.ajax({
                type: 'POST',
                url: typeof ajaxurl != 'undefined' ? ajaxurl : yith_ajax.url,
                data: {
                    action:     'yith_license_banner_dismiss',
                    _wpnonce:   license_notice.data( 'nonce' )
                }
            });
        });
})(jQuery);