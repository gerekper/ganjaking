var mepr_load_driptags_accounts_dropdown = function (id, apikey, wpnonce) {
    (function($) {
        if( apikey == '' ) { return; }

        var account_id = $(id).data('accountid');

        var args = {
             action: 'mepr_drip_tags_get_accounts',
             apikey: apikey,
             wpnonce: wpnonce
         };

        $.ajax({
            async: false,
            type: 'POST',
            dataType : "json",
            url: ajaxurl,
            data : args,
            success: function(res){
                if( res != 'error' ) {
                    var options = '';
                    var selected = '';

                    $.each( res, function( index, account ) {
                        selected = ( ( account_id == account.id ) ? ' selected' : '' );
                        options += '<option value="' + account.id + '"' + selected + '>' + account.name + '</option>';
                    });

                    $(id).html(options);
                }
            }
        });

    })(jQuery);
};
