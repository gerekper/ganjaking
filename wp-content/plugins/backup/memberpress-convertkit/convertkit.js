var mepr_load_convertkit_tags_dropdown = function(id, api_secret, wpnonce) {
  (function($) {
    if(api_secret == '') { return; }

    var tag_id = $(id).data('tagid');

    var args = {
      action: 'mepr_convertkit_get_tags',
      api_secret: api_secret,
      wpnonce: wpnonce
    };

    $.post(ajaxurl, args, function(res) {
      if(res.length > 0) {
        var options = '';
        var selected = '';

        $.each(res, function(index, tag) {
          selected = ((tag_id == tag.id) ? ' selected' : '');
          options += '<option value="' + tag.id + '"' + selected + '>' + tag.name + '</option>';
        });

        $(id).html(options);
      }
    }, 'json');
  })(jQuery);
};
