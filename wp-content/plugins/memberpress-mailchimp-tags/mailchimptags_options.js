jQuery(document).ready(function($) {
  // MailChimp API stuff
  var mepr_check_mailchimptags_apikey = function(apikey, wpnonce) {
    if(apikey == '') { return; }

    var args = {
      action: 'mepr_mailchimptags_ping_apikey',
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post( ajaxurl, args, function(res) {
      if(res.status == 'failed') {
        $('#mepr-mailchimptags-valid').hide();
        $('#mepr-mailchimptags-invalid').html(res.message);
        $('#mepr-mailchimptags-invalid').fadeIn();
        $('select#meprmailchimptags_list_id').html('');
        $('select#meprmailchimptags_form_id').html('');
      }
      else {
        $('#mepr-mailchimptags-invalid').hide();
        $('#mepr-mailchimptags-valid').html(res.message);
        $('#mepr-mailchimptags-valid').fadeIn();
        mepr_load_mailchimptags_lists_dropdown('select#meprmailchimptags_list_id', apikey, wpnonce);
      }
    }, 'json' );
  }

  //MailChimp enabled/disable checkbox
  if($('#meprmailchimptags_enabled').is(":checked")) {
    mepr_check_mailchimptags_apikey($('#meprmailchimptags_api_key').val(), MeprMailChimpTags.wpnonce);
    $('div#mailchimptags_hidden_area').show();
  } else {
    $('div#mailchimptags_hidden_area').hide();
  }
  $('#meprmailchimptags_enabled').click(function() {
    if($('#meprmailchimptags_enabled').is(":checked")) {
      mepr_check_mailchimptags_apikey($('#meprmailchimptags_api_key').val(), MeprMailChimpTags.wpnonce);
    }
    $('div#mailchimptags_hidden_area').slideToggle('fast');
  });

  //MailChimp enabled/disable opt-in checkbox
  if($('#meprmailchimptags_optin').is(":checked")) {
    $('div#meprmailchimptags-optin-text').show();
  } else {
    $('div#meprmailchimptags-optin-text').hide();
  }
  $('#meprmailchimptags_optin').click(function() {
    $('div#meprmailchimptags-optin-text').slideToggle('fast');
  });

  // MailChimp Actions
  if($('#meprmailchimptags_enabled').is(':checked')) {
    mepr_check_mailchimptags_apikey($('#meprmailchimptags_api_key').val(), MeprMailChimpTags.wpnonce);
  }

  $('#meprmailchimptags_api_key').blur( function(e) {
    mepr_check_mailchimptags_apikey($(this).val(), MeprMailChimpTags.wpnonce);
  });

  $('select#meprmailchimptags_list_id').change(function() {
    $('select#meprmailchimptags_tag_id').html('');
    mepr_load_mailchimptags_tags_dropdown('select#meprmailchimptags_tag_id', $(this).val(), $('#meprmailchimptags_api_key').val(), MeprMailChimpTags.wpnonce);
  });
}); //End main document.ready func
