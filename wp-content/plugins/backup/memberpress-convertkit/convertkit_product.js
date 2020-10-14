jQuery(document).ready(function($) {
  if($('#meprconvertkit_tag_override').is(":checked")) {
    mepr_load_convertkit_tags_dropdown( '#meprconvertkit_active_tag_override_id',
                                        $('#meprconvertkit_tag_override').data('api_secret'),
                                        MeprProducts.wpnonce );
    mepr_load_convertkit_tags_dropdown( '#meprconvertkit_inactive_tag_override_id',
                                        $('#meprconvertkit_tag_override').data('api_secret'),
                                        MeprProducts.wpnonce );
    $('div#meprconvertkit_override_area').show();
  } else {
    $('div#meprconvertkit_override_area').hide();
  }

  $('#meprconvertkit_tag_override').click(function() {
    if($('#meprconvertkit_tag_override').is(":checked")) {
      mepr_load_convertkit_tags_dropdown('#meprconvertkit_active_tag_override_id',
                                        $('#meprconvertkit_tag_override').data('api_secret'),
                                        MeprProducts.wpnonce);
      mepr_load_convertkit_tags_dropdown('#meprconvertkit_inactive_tag_override_id',
                                        $('#meprconvertkit_tag_override').data('api_secret'),
                                        MeprProducts.wpnonce);
    }

    $('div#meprconvertkit_override_area').slideToggle();
  });
}); //End main document.ready func
