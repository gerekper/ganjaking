<?php


add_action('wp_ajax_FEML.fileTypes.saveAllowed', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['allowed']) || !is_array($_POST['allowed'])) feml_error_perms();

  $newAllowed = $_POST['allowed'];

  $oldAllowed = feml_services_fileTypes_getAllowed();
  $oldAllowed = array_keys($oldAllowed);

  $mimeTypes = feml_services_fileTypes_getAll();
  $uploadMimes = feml_services_fileTypes_getUpload();

  $add = array_diff($newAllowed, $oldAllowed);
  $remove = array_diff($oldAllowed, $newAllowed);

  foreach ($add as $ext) {
    if (!isset($uploadMimes['remove'][$ext])) {
      if (isset($mimeTypes[$ext])) {
        $uploadMimes['add'][$ext] = $mimeTypes[$ext];
      }
    } else {
      unset($uploadMimes['remove'][$ext]);
    }
  }

  foreach ($remove as $ext) {
    if (!isset($uploadMimes['add'][$ext])) {
      if (isset($mimeTypes[$ext])) {
        $uploadMimes['remove'][$ext] = 1;
      }
    } else {
      unset($uploadMimes['add'][$ext]);
    }
  }

  update_option('feml_upload_mimes', serialize($uploadMimes), false);

  wp_send_json_success();
});