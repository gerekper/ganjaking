<?php


add_action('wp_ajax_FEML.items.moveList', function() {
  if (!current_user_can('upload_files')) feml_error_perms();

  if (!isset($_POST['type'])) feml_error_perms();

  if (isset($_POST['folders']) && !is_array($_POST['folders'])) feml_error_perms();
  if (isset($_POST['files']) && !is_array($_POST['files'])) feml_error_perms();

  if (!isset($_POST['folders']) && !isset($_POST['files'])) feml_error_perms();
  if (!isset($_POST['parent'])) feml_error_perms();

  $type = $_POST['type'];
  $folders = isset($_POST['folders']) ? array_map('intval', $_POST['folders']) : array();
  $files = isset($_POST['files']) ? array_map('intval', $_POST['files']) : array();
  $parent = intval($_POST['parent']);

  if ($parent !== 0) {
    $parentFolder = feml_models_folders_getOne($parent);
    if (!$parentFolder) feml_error_perms();
  }

  foreach ($folders as $id) {
    $options = array('parent' => $parent);

    $res = wp_update_term($id, 'feml-folder', $options);
    if (is_wp_error($res)) feml_error_wp($res);
  }

  foreach ($files as $id) {
    if ($type === 'link') {
      $meta = wp_get_attachment_metadata($id);

      $originalID = isset($meta['feml_shortcut_parent'])
        ? $meta['feml_shortcut_parent']
        : $id;

      $originalPost = feml_models_posts_getPost($originalID);
      if (!$originalPost) return feml_error_perms();

      unset($originalPost->ID);
      $originalPost->post_date = current_time('mysql');
      $originalPost->post_date_gmt = current_time('mysql', 1);
      $newID = feml_models_posts_insertPost($originalPost);

      $originalMetas = feml_models_posts_getMetas($originalID);
      feml_models_posts_insertMetas($newID, $originalMetas);

      $newMeta = wp_get_attachment_metadata($newID);
      $newMeta['feml_shortcut_parent'] = $originalID;
      unset($newMeta['feml_shortcut_childs']);
      wp_update_attachment_metadata($newID, $newMeta);

      $originalMeta = $id !== $originalID
        ? wp_get_attachment_metadata($originalID)
        : $meta;

      $originalMeta['feml_shortcut_childs'] = isset($originalMeta['feml_shortcut_childs'])
        ? $originalMeta['feml_shortcut_childs']
        : array();

      $originalMeta['feml_shortcut_childs'][] = $newID;
      wp_update_attachment_metadata($originalID, $originalMeta);
      
      $res = wp_set_post_terms($newID, array($parent), 'feml-folder');
      if (is_wp_error($res)) feml_error_wp($res);
    } else {
      $terms = $parent !== 0 ? array($parent) : array();

      $res = wp_set_post_terms($id, $terms, 'feml-folder');
      if (is_wp_error($res)) feml_error_wp($res);
    }
  }

  $counts = feml_models_folders_getCounts();

  for ($i = 0; $i < count($counts); $i++) {
    $counts[$i] = array($counts[$i]->term_id, $counts[$i]->count);
  }

  $counts[] = array(0, feml_models_folders_getRootCount());

  wp_send_json_success($counts);
});
