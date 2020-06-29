<?php


add_action('wp_ajax_FEML.files.regenerate', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['id'])) feml_error_perms();

  $id = intval($_POST['id']);

  $fullsizepath = get_attached_file($id);

  if ($fullsizepath === false || !file_exists($fullsizepath)) {
    feml_error(__('The originally uploaded image file cannot be found at ') . esc_html($fullsizepath));
  }

  $metadata = wp_generate_attachment_metadata($id, $fullsizepath);
  if (is_wp_error($metadata)) feml_error_wp($metadata);

  if (empty($metadata)) {
    feml_error(__('Error happened during thumbnails regeneration.'));
  }

  wp_update_attachment_metadata($id, $metadata);

  wp_send_json_success();
});
