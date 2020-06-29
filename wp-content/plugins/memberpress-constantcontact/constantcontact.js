var mepr_load_constantcontact_lists_dropdown = function (id, apikey, access_token, wpnonce) {
    (function($) {
        if( apikey == '' || access_token == '' ) { return; }

        var list_id = $(id).data('listid');

        var args = {
            action: 'mepr_constantcontact_get_lists',
            apikey: apikey,
            access_token: access_token,
            wpnonce: wpnonce
        };

        $.post( ajaxurl, args, function(res) {

            if(res != 'error') {
                var options = '';
                var selected = '';

                $.each( res, function( index, list ) {
                    selected = ( ( list_id == list.id ) ? ' selected' : '' );
                    options += '<option value="' + list.id + '"' + selected + '>' + list.name + '</option>';
                });

                $(id).html(options);
            }
        }, 'json' );
    })(jQuery);
};
