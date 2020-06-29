var mepr_load_getresponse_lists_dropdown = function (id, apikey, wpnonce) {
  (function($) {
    if( apikey == '' ) { return; }

    var list_id = $(id).data('listid');

    var args = {
      action: 'mepr_get_campaigns',
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post(ajaxurl, args, function(res) {
      if( res.total > 0 ) {
        var options = '';
        var selected = '';

        $.each(res.data, function (index, list) {
          selected = ((list_id == list.list_id) ? ' selected' : '');
          options += '<option value="' + list.list_id + '"' + selected + '>' + list.list_name + '</option>';
        });

        $(id).html(options);
      }
    }, 'json');
  })(jQuery);
};

