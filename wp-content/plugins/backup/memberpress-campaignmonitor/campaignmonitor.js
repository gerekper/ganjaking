var mepr_load_campaignmonitor_lists_dropdown = function (id, clientid, apikey, wpnonce) {
    (function($) {
        if( clientid == '' || apikey == '' ) { return; }

        var list_id = $(id).data('listid');

        var args = {
            action: 'mepr_campaignmonitor_get_lists',
            clientid: clientid,
            apikey: apikey,
            wpnonce: wpnonce
        };

        $.post( ajaxurl, args, function(res) {
            if(res != 'error') {
                var options = '';
                var selected = '';

                $.each( res, function( index, list ) {
                    selected = ( ( list_id == list.ListID ) ? ' selected' : '' );
                    options += '<option value="' + list.ListID + '"' + selected + '>' + list.Name + '</option>';
                });

                $(id).html(options);
            }
        }, 'json' );
    })(jQuery);
};
