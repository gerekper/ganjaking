<?php


add_action('wp_ajax_FEML.folders.createPaths', function() {
 if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['paths'])) feml_error_perms();
  if (!is_array($_POST['paths'])) feml_error_perms();
  if (!isset($_POST['parent'])) feml_error_perms();

  $paths = $_POST['paths'];
  $parent = intval($_POST['parent']);

  if ($parent !== 0) {
    $parentFolder = feml_models_folders_getOne($parent);
    if (!$parentFolder) feml_error_perms();
  }

  $newPaths = array();
  $newFolders = array();

  foreach ($paths as $path) {
    $names = explode('/', $path);
    $subParent = $parent;

    foreach ($names as $name) {
      $term = term_exists($name, 'feml-folder', $subParent);

      if ($term !== 0 && $term !== null) {
        $subParent = intval($term['term_id']);
      } else {
        $options = array(
          'slug' => 'feml-folder-' . mt_rand(1000000000, 9999999999)
        );

        $res = wp_insert_term($name, 'feml-folder', $options);
        if (is_wp_error($res)) feml_error_wp($res);

        $options = array(
          'term_group' => get_current_user_id(),
          'parent' => $subParent
        );

        $res = wp_update_term($res['term_id'], 'feml-folder', $options);
        if (is_wp_error($res)) feml_error_wp($res);

        $newFolders[] = array(
          'id' => $res['term_id'],
          'parent' => $subParent,
          'name' => $name,
        );

        $subParent = $res['term_id'];
      }
    }

    $newPaths[$path] = $subParent;
  }

  wp_send_json_success(array(
    'paths' => $newPaths,
    'folders' => $newFolders
  ));
});
