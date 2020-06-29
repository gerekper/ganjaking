<?php


add_action('wp_ajax_FEML.files.delete', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['id'])) feml_error_perms();

  $id = intval($_POST['id']);

  $res = wp_delete_attachment($id, true);
  if ($res === false) feml_error(__('Error happened during file deletion.'));

  wp_send_json_success();
});
