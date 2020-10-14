var mepr_load_mailchimptags_lists_dropdown = function(id, apikey, wpnonce) {
  (function($) {
      if(apikey == '') { return; }

      var list_id = $(id).data('listid').toString();

      var args = {
        action: 'mepr_mailchimptags_get_lists',
        apikey: apikey,
        wpnonce: wpnonce
      };

      $.post( ajaxurl, args, function(res) {
        if(res !== undefined && res.lists !== undefined) {
          var options = '';
          var selected = '';
          var count = 0;

          $.each(res.lists, function(index, list) {
            //maybe populate our list_id if one isn't already set
            if(count == 0 && list_id.length == 0) {
              list_id = list.id;
            }

            if($.isNumeric(index)) {
              selected = ((list_id == list.id)?' selected':'');
              options += '<option value="' + list.id + '"' + selected + '>' + list.name + '</option>';
            }
            count++;
          });

          if(list_id.length > 0) {
            mepr_load_mailchimptags_tags_dropdown('select#meprmailchimptags_tag_id', list_id, apikey, wpnonce);
          }

          $(id).html(options);
        }
      }, 'json' );
  })(jQuery);
};

var mepr_load_mailchimptags_tags_dropdown = function(id, list_id, apikey, wpnonce) {
  (function($) {
      if(apikey == '') { return; }

      var tag_id = $(id).data('tagid');

      var args = {
        action: 'mepr_mailchimptags_get_tags',
        listid: list_id,
        apikey: apikey,
        wpnonce: wpnonce
      };

      $.post( ajaxurl, args, function(res) {
        if(res !== undefined && res.merge_fields !== undefined) {
          var options = '';
          var selected = '';

          $.each(res.merge_fields, function(index, tag) {
            if($.isNumeric(index)) {
              selected = ((tag_id == tag.merge_id)?' selected':'');
              options += '<option value="' + tag.merge_id + '"' + selected + '>' + tag.name + '</option>';
            }
          });

          $(id).html(options);
        }
      }, 'json' );
  })(jQuery);
};
