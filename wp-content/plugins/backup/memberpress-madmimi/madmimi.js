var mepr_load_madmimi_lists_dropdown = function (id, username, apikey, wpnonce) {
  (function($) {
    if( username == '' ) { return; }
    if( apikey == '' ) { return; }

    var list_id = $(id).data('listid');

    var args = {
      action: 'mepr_madmimi_get_lists',
      username: username,
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post( ajaxurl, args, function(res) {
      if( res.length > 0 ) {
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
