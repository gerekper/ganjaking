<?php


add_action('wp_ajax_FEML.folders.create', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['name'])) feml_error_perms();
  if (!isset($_POST['parent'])) feml_error_perms();

  $name = $_POST['name'];
  $parent = intval($_POST['parent']);

  if ($parent !== 0) {
    $parentFolder = feml_models_folders_getOne($parent);
    if (!$parentFolder) feml_error_perms();
  }

  $options = array(
    'slug' => 'feml-folder-' . mt_rand(1000000000, 9999999999)
  );

  $res = wp_insert_term($name, 'feml-folder', $options);
  if (is_wp_error($res)) feml_error_wp($res);

  $options = array(
    'term_group' => get_current_user_id(),
    'parent' => $parent
  );

  $res = wp_update_term($res['term_id'], 'feml-folder', $options);
  if (is_wp_error($res)) feml_error_wp($res);

  wp_send_json_success(array(
    'id' => $res['term_id']
  ));
});
