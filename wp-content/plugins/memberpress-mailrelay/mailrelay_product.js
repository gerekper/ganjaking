jQuery(document).ready(function($) {
  if($('#meprmailrelay_group_override').is(":checked")) {
    mepr_load_mailrelay_groups_dropdown('#meprmailrelay_group_override_id',
                                      $('#meprmailrelay_group_override').data('domain'),
                                      $('#meprmailrelay_group_override').data('apikey'),
                                      MeprProducts.wpnonce);
    $('div#meprmailrelay_override_area').show();
  } else {
    $('div#meprmailrelay_override_area').hide();
  }

  $('#meprmailrelay_group_override').click(function() {
    if($('#meprmailrelay_group_override').is(":checked")) {
      mepr_load_mailrelay_groups_dropdown('#meprmailrelay_group_override_id',
                                        $('#meprmailrelay_group_override').data('domain'),
                                        $('#meprmailrelay_group_override').data('apikey'),
                                        MeprProducts.wpnonce);
    }
    $('div#meprmailrelay_override_area').slideToggle();
  });
}); //End main document.ready func
