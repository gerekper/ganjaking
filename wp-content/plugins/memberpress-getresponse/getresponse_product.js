jQuery(document).ready(function($) {
  //trial period
  if($('#meprgetresponse_list_override').is(":checked")) {
    mepr_load_getresponse_lists_dropdown( '#meprgetresponse_list_override_id',
                                        $('#meprgetresponse_list_override').data('apikey'),
                                        MeprProducts.wpnonce );
    $('div#meprgetresponse_override_area').show();
  } else {
    $('div#meprgetresponse_override_area').hide();
  }
  $('#meprgetresponse_list_override').click(function() {
    if($('#meprgetresponse_list_override').is(":checked")) {
      mepr_load_getresponse_lists_dropdown( '#meprgetresponse_list_override_id',
                                          $('#meprgetresponse_list_override').data('apikey'),
                                          MeprProducts.wpnonce );
    }
    $('div#meprgetresponse_override_area').slideToggle();
  });
}); //End main document.ready func

