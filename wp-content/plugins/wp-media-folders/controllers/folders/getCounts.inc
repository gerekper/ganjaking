<?php


add_action('wp_ajax_FEML.folders.getCounts', function() {
  if (!current_user_can('upload_files')) {
    feml_error_perms();
  }

  $counts = feml_models_folders_getCounts();

  for ($i = 0; $i < count($counts); $i++) {
    $counts[$i] = array(
      $counts[$i]->term_id,
      $counts[$i]->count
    );
  }

  $counts[] = array(0, feml_models_folders_getRootCount());

  wp_send_json_success($counts);
});
