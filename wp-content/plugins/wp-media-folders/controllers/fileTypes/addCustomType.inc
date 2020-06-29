<?php


add_action('wp_ajax_FEML.fileTypes.addCustomType', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['ext']) || !$_POST['ext']) feml_error_perms();
  if (!isset($_POST['mimeType']) || !$_POST['mimeType']) feml_error_perms();

  $ext = $_POST['ext'];
  $mimeType = $_POST['mimeType'];

  $mimeTypes = feml_services_fileTypes_getAll();
  if (isset($mimeTypes[$ext])) feml_error(__('File extension ".' . $ext . '" already exists.'));

  $customMimeTypes = feml_services_fileTypes_getCustom();
  $uploadMimeTypes = feml_services_fileTypes_getUpload();

  $customMimeTypes[$ext] = $mimeType;
  update_option('feml_mime_types', serialize($customMimeTypes), false);

  $uploadMimeTypes['add'][$ext] = $mimeType;
  update_option('feml_upload_mimes', serialize($uploadMimeTypes), false);

  wp_send_json_success();
});