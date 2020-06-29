<?php


add_action('wp_ajax_FEML.fileTypes.deleteType', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['ext']) || !$_POST['ext']) feml_error_perms();

  $ext = $_POST['ext'];

  $customMimeTypes = feml_services_fileTypes_getCustom();
  if (!isset($customMimeTypes[$ext])) feml_error(__('File extension ".' . $ext . '" does not exist.'));

  unset($customMimeTypes[$ext]);
  update_option('feml_mime_types', serialize($customMimeTypes), false);

  $uploadMimeTypes = feml_services_fileTypes_getUpload();

  if (isset($uploadMimeTypes['add'][$ext])) {
    unset($uploadMimeTypes['add'][$ext]);
    update_option('feml_upload_mimes', serialize($uploadMimeTypes), false);
  }

  wp_send_json_success();
});
