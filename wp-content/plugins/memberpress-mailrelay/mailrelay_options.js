jQuery(document).ready(function($) {
  // Mailrelay API stuff
  var mepr_check_mailrelay_apikey = function(domain, apikey, wpnonce) {
    if(domain == '') { return; }
    if(apikey == '') { return; }

    var args = {
      action: 'mepr_mailrelay_ping_apikey',
      domain: domain,
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post(ajaxurl, args, function(res) {
      if('error' in res) {
        $('#mepr-mailrelay-valid').hide();
        $('#mepr-mailrelay-invalid').html( res.error );
        $('#mepr-mailrelay-invalid').fadeIn();
        $('select#meprmailrelay_group_id').html('');
      }
      else {
        $('#mepr-mailrelay-invalid').hide();
        $('#mepr-mailrelay-valid').html(res.msg);
        $('#mepr-mailrelay-valid').fadeIn();
        mepr_load_mailrelay_groups_dropdown('select#meprmailrelay_group_id', domain, apikey, wpnonce);
      }
    }, 'json');
  }

  //Mailrelay enabled/disable checkbox
  if($('#meprmailrelay_enabled').is(":checked")) {
    mepr_check_mailrelay_apikey($('#meprmailrelay_domain').val(), $('#meprmailrelay_api_key').val(), MeprMailrelay.wpnonce);
    $('div#mailrelay_hidden_area').show();
  } else {
    $('div#mailrelay_hidden_area').hide();
  }
  $('#meprmailrelay_enabled').click(function() {
    if($('#meprmailrelay_enabled').is(":checked")) {
      mepr_check_mailrelay_apikey($('#meprmailrelay_domain').val(), $('#meprmailrelay_api_key').val(), MeprMailrelay.wpnonce);
    }
    $('div#mailrelay_hidden_area').slideToggle('fast');
  });

  var action = ($('#meprmailrelay_optin').is(":checked")?'show':'hide');

  $('#meprmailrelay-optin-text')[action]();
  $('#meprmailrelay_optin').click(function() {
    $('#meprmailrelay-optin-text')['slideToggle']('fast');
  });

  $('#meprmailrelay_domain').blur( function(e) {
    mepr_check_mailrelay_apikey($(this).val(), $('#meprmailrelay_api_key').val(), MeprMailrelay.wpnonce);
  });

  $('#meprmailrelay_api_key').blur(function(e) {
    mepr_check_mailrelay_apikey($('#meprmailrelay_domain').val(), $(this).val(), MeprMailrelay.wpnonce);
  });
}); //End main document.ready func
