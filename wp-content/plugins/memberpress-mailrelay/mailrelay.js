var mepr_load_mailrelay_groups_dropdown = function(id, domain, apikey, wpnonce) {
  (function($) {
    if(domain == '') { return; }
    if(apikey == '') { return; }

    var group_id = $(id).data('groupid');

    var args = {
      action: 'mepr_mailrelay_get_groups',
      domain: domain,
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post(ajaxurl, args, function(res) {
      if(res.length > 0) {
        var options = '<option value="please-select">' + MeprMailrelayL10n.please + '</option>';
        var selected = '';

        $.each(res, function(index, group) {
          selected = ((group_id == group.id) ? ' selected' : '');
          options += '<option value="' + group.id + '"' + selected + '>' + group.name + '</option>';
        });

        $(id).html(options);
      }
    }, 'json');
  })(jQuery);
};
