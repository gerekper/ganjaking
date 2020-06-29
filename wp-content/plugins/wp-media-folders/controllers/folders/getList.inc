<?php


add_action('wp_ajax_FEML.folders.getList', function() {
  if (!current_user_can('upload_files')) {
    feml_error_perms();
  }

  $folders = feml_models_folders_getList();

  for ($i = 0; $i < count($folders); $i++) {
    $folders[$i] = array(
      $folders[$i]->term_id,
      $folders[$i]->parent,
      $folders[$i]->count,
      $folders[$i]->name
    );
  }

  $folders[] = array(
    0,
    null,
    feml_models_folders_getRootCount(),
    __('Media Library')
  );

  wp_send_json_success($folders);
});
