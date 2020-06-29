<?php


add_action('wp_ajax_FEML.settings.getData', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  $results = array();

  $results['restrictByOwner'] = get_option('feml_restrict_by_owner', 'false') === 'true';

  wp_send_json_success($results);
});
