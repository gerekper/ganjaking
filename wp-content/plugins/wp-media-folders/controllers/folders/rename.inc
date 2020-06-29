<?php


add_action('wp_ajax_FEML.folders.rename', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['id'])) feml_error_perms();
  if (!isset($_POST['name'])) feml_error_perms();

  $id = intval($_POST['id']);
  $name = $_POST['name'];

  if (strlen($name) === 0) feml_error_perms();
  $folder = feml_models_folders_getOne($id);

  if (!$folder) feml_error_perms();
  if ($name === $folder->name) feml_error_perms();

  $options = array('name' => $name);

  $res = wp_update_term($id, 'feml-folder', $options);
  if (is_wp_error($res)) feml_error_wp($res);

  wp_send_json_success();
});
