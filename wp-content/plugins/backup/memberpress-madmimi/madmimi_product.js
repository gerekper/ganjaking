jQuery(document).ready(function($) {
  if($('#meprmadmimi_list_override').is(":checked")) {
    mepr_load_madmimi_lists_dropdown( '#meprmadmimi_list_override_id',
                                      $('#meprmadmimi_list_override').data('username'),
                                      $('#meprmadmimi_list_override').data('apikey'),
                                      MeprProducts.wpnonce );
    $('div#meprmadmimi_override_area').show();
  } else {
    $('div#meprmadmimi_override_area').hide();
  }

  $('#meprmadmimi_list_override').click(function() {
    if($('#meprmadmimi_list_override').is(":checked")) {
      mepr_load_madmimi_lists_dropdown( '#meprmadmimi_list_override_id',
                                        $('#meprmadmimi_list_override').data('username'),
                                        $('#meprmadmimi_list_override').data('apikey'),
                                        MeprProducts.wpnonce );
    }
    $('div#meprmadmimi_override_area').slideToggle();
  });
}); //End main document.ready func
