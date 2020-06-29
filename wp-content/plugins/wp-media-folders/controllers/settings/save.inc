<?php


add_action('wp_ajax_FEML.settings.save', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  $settings = $_POST['settings'];

  update_option('feml_restrict_by_owner', $settings['restrictByOwner'], false);

  wp_send_json_success();
});
