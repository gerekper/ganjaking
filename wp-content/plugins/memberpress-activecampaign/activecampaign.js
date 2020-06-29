var mepr_load_activecampaign_lists_dropdown = function (id, account, apikey, wpnonce) {
    (function($) {
        if( apikey == '' || account == '' ) { return; }

        var list_id = $(id).data('listid');

        var args = {
            action: 'mepr_activecampaign_get_lists',
            account: account,
            apikey: apikey,
            wpnonce: wpnonce
        };

        $.post( ajaxurl, args, function(res) {

            if(res !== undefined && res.result_code !== undefined && res.result_code == 1) {
                var options = '';
                var selected = '';

                $.each( res, function( index, list ) {
                     if($.isNumeric(index)){
                        selected = ( ( list_id == list.id ) ? ' selected' : '' );
                        options += '<option value="' + list.id + '"' + selected + '>' + list.name + '</option>';
                    }
                });

                $(id).html(options);
            }
        }, 'json' );
    })(jQuery);
};
