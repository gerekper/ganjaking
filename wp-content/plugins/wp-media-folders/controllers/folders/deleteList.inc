<?php


add_action('wp_ajax_FEML.folders.deleteList', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['ids'])) feml_error_perms();
  if (!is_array($_POST['ids'])) feml_error_perms();

  $ids = array_map('intval', $_POST['ids']);

  foreach ($ids as $id) {
    $res = wp_delete_term($id, 'feml-folder');
    if (is_wp_error($res)) feml_error_wp($res);
  }

  wp_send_json_success();
});
