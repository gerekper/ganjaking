<?php


add_action('wp_ajax_FEML.files.getNestedList', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (isset($_POST['folders']) && !is_array($_POST['folders'])) feml_error_perms();
  if (isset($_POST['files']) && !is_array($_POST['files'])) feml_error_perms();

  if (!isset($_POST['all'])) feml_error_perms();
  $all = $_POST['all'] === 'true' ? true : false;

  if (!isset($_POST['folders']) && !isset($_POST['files']) && !$all) feml_error_perms();

  $folders = isset($_POST['folders']) ? array_map('intval', $_POST['folders']) : array();
  $files = isset($_POST['files']) ? array_map('intval', $_POST['files']) : array();

  $results = array();

  if (!$all) {
    $files1 = count($files) > 0
      ? feml_models_files_getFiles($files)
      : array();

    $files2 = count($folders) > 0
      ? feml_models_files_getFilesByFolders($folders)
      : array();

    $results = array_merge($files1, $files2);
  } else {
    $results = feml_models_files_getFilesAll();
  }

  for ($i = 0; $i < count($results); $i++) {
    $results[$i] = array(
      $results[$i]->ID,
      $results[$i]->post_title
    );
  }

  wp_send_json_success($results);
});
