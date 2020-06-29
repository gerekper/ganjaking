jQuery(document).ready(function($) {
  // ConvertKit API stuff
  var mepr_check_convertkit_api_secret = function(api_secret, wpnonce) {
    if(api_secret == '') { return; }

    var args = {
      action: 'mepr_convertkit_ping_api_secret',
      api_secret: api_secret,
      wpnonce: wpnonce
    };

    $.post(ajaxurl, args, function(res) {
      if('error' in res) {
        $('#mepr-convertkit-valid').hide();
        $('#mepr-convertkit-invalid').html(res.error);
        $('#mepr-convertkit-invalid').fadeIn();
        $('select#meprconvertkit_tag_id').html('');
      }
      else {
        $('#mepr-convertkit-invalid').hide();
        $('#mepr-convertkit-valid').html(res.msg);
        $('#mepr-convertkit-valid').fadeIn();
        mepr_load_convertkit_tags_dropdown('select#meprconvertkit_tag_id', api_secret, wpnonce);
      }
    }, 'json');
  }

  //ConvertKit enabled/disable checkbox
  if($('#meprconvertkit_enabled').is(":checked")) {
    mepr_check_convertkit_api_secret($('#meprconvertkit_api_secret').val(), MeprConvertKit.wpnonce);
    $('div#convertkit_hidden_area').show();
  } else {
    $('div#convertkit_hidden_area').hide();
  }
  $('#meprconvertkit_enabled').click(function() {
    if($('#meprconvertkit_enabled').is(":checked")) {
      mepr_check_convertkit_api_secret($('#meprconvertkit_api_secret').val(), MeprConvertKit.wpnonce);
    }
    $('div#convertkit_hidden_area').slideToggle('fast');
  });

  var action = ($('#meprconvertkit_optin').is(":checked")?'show':'hide');

  $('#meprconvertkit-optin-text')[action]();
  $('#meprconvertkit_optin').click(function() {
    $('#meprconvertkit-optin-text')['slideToggle']('fast');
  });

  $('#meprconvertkit_api_secret').blur(function(e) {
    mepr_check_convertkit_api_secret($(this).val(), MeprConvertKit.wpnonce);
  });
}); //End main document.ready func
