jQuery(document).ready(function($) {
  if($('#meprmailchimptags_add_tags').is(":checked")) {
    $('div#meprmailchimptags_tags_area').show();
    mepr_load_mailchimptags_tags_dropdown('#meprmailchimptags_tag_id', $('#meprmailchimptags_add_tags').data('listid'), $('#meprmailchimptags_add_tags').data('apikey'), MeprMailChimpTags.wpnonce);
  } else {
    $('div#meprmailchimptags_tags_area').hide();
  }

  $('#meprmailchimptags_add_tags').click(function() {
    $('div#meprmailchimptags_tags_area').slideToggle();
    if($(this).is(":checked")) {
      mepr_load_mailchimptags_tags_dropdown('#meprmailchimptags_tag_id', $('#meprmailchimptags_add_tags').data('listid'), $('#meprmailchimptags_add_tags').data('apikey'), MeprMailChimpTags.wpnonce);
    }
  });
});
