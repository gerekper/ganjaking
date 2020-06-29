(function ($) {
	jQuery(document).on( 'click', '.plus-key-notify .notice-dismiss', function() {

    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'plus_key_notice'
        }
    })

})
})(window.jQuery);
