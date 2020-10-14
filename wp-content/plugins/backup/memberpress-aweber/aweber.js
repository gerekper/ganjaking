var mepr_load_aweber_list_dropdown = function (id, wpnonce) {
  (function($) {
    var args = {
      action: 'mepr_get_aweber_lists',
      wpnonce: wpnonce
    };

    var list_id = $(id).data('listid');

    $(id+'-loading').show();

    $.post(ajaxurl, args,
      function (res) {
        $(id+'-loading').hide();

        // Check to see if the action returned an error
        if ('error' in res) {
          // Do nothing
        }
        else {
          var options = '';
          var selected = '';

          $.each(res.lists, function (index, value) {
            selected = ((list_id == index) ? ' selected' : '');
            options += '<option value="' + index + '"' + selected + '>' + value + '</option>';
          });

          $(id).html(options);
        }
      },
      'json'
    );
  })(jQuery);
};

